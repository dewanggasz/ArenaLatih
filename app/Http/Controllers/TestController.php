<?php

namespace App\Http\Controllers;

// --- KUMPULAN SEMUA USE STATEMENT YANG DIBUTUHKAN ---
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestAnswer;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    /**
     * Menampilkan dashboard dengan daftar tes, riwayat, dan kategori untuk filter.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil SEMUA hasil tes untuk memeriksa status di kartu latihan
        $allUserResults = $user->testResults()->get();
        
        // PERBAIKAN: Ambil HANYA hasil yang sudah selesai untuk ditampilkan di Riwayat
        $completedUserResults = $user->testResults()
                                    ->where('status', 'completed')
                                    ->with('test')
                                    ->latest()
                                    ->get();

        $tests = Test::with(['subCategory.category'])
                    ->withCount('questions')
                    ->latest()
                    ->get();
        
        $categories = Category::whereHas('subCategories.tests')->get();

        return view('dashboard', [
            'tests' => $tests,
            'userResults' => $allUserResults, // Ini digunakan untuk menentukan status kartu
            'completedResults' => $completedUserResults, // Ini khusus untuk tab riwayat
            'categories' => $categories,
        ]);
    }

    /**
     * Memulai atau melanjutkan sebuah latihan.
     */
    public function start(Test $test)
    {
        $user = Auth::user();

        $existingResult = TestResult::where('user_id', $user->id)
                                    ->where('test_id', $test->id)
                                    ->first();

        if ($existingResult && $existingResult->status === 'completed') {
            return redirect()->route('test.show', $test->id);
        }

        if ($existingResult && $existingResult->status === 'in_progress') {
            return redirect()->route('test.show', $test->id);
        }

        TestResult::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'questions_count' => $test->questions()->count(),
            'score' => 0, // ⬅️ Tambahkan nilai default score
            'correct_answers_count' => 0,
        ]);

        return redirect()->route('test.show', $test->id);
    }
    
    /**
     * Menampilkan halaman pengerjaan tes atau mode pembahasan.
     */
    public function show(Test $test)
    {
        $user = Auth::user();
        $test->load('questions.choices');

        $result = TestResult::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->firstOrFail(); 

        if ($result->status === 'completed') {
            $result->load('answers.question');
            $userAnswers = $result->answers->pluck('choice_id', 'question_id')->filter();
            $userEssayAnswers = $result->answers->whereNotNull('essay_answer')->pluck('essay_answer', 'question_id');
            $aiFeedbacks = $result->answers->whereNotNull('ai_feedback')->pluck('ai_feedback', 'question_id');
            $aiScores = $result->answers->whereNotNull('ai_score')->pluck('ai_score', 'question_id');

            return view('test.show', [
                'test' => $test,
                'isReviewMode' => true,
                'userAnswers' => $userAnswers,
                'userEssayAnswers' => $userEssayAnswers,
                'aiFeedbacks' => $aiFeedbacks,
                'aiScores' => $aiScores,
            ]);
        }

        $elapsed = now()->diffInSeconds($result->started_at);
        $totalDuration = $test->duration_minutes * 60;
        $timeRemaining = $totalDuration - $elapsed;

        return view('test.show', [
            'test' => $test,
            'isReviewMode' => false,
            'timeRemaining' => $timeRemaining > 0 ? $timeRemaining : 0,
        ]);
    }

    /**
     * Menerima jawaban, mengevaluasi, dan menyelesaikan hasil.
     */
    public function submit(Request $request, Test $test)
    {
        $user = Auth::user();
        $testResult = TestResult::where('user_id', $user->id)
                                ->where('test_id', $test->id)
                                ->where('status', 'in_progress')
                                ->firstOrFail();

        $submittedAnswers = $request->input('answers', []);
        foreach ($submittedAnswers as $question_id => $answer) {
            if (!empty($answer)) {
                TestAnswer::updateOrCreate(
                    ['test_result_id' => $testResult->id, 'question_id' => $question_id],
                    [
                        'choice_id' => is_numeric($answer) ? $answer : null,
                        'essay_answer' => is_string($answer) && !is_numeric($answer) ? $answer : null,
                    ]
                );
            }
        }
        
        if ($test->result_type === 'numeric') {
            $this->evaluateNumericTest($test, $testResult);
        } elseif ($test->result_type === 'descriptive') {
            $this->evaluateDescriptiveTest($test, $testResult);
        }
        
        $testResult->status = 'completed';
        $testResult->save();
        
        $this->generateShareImage($testResult);
        return redirect()->route('test.result', ['testResult' => $testResult->id]);
    }
    

    /**
     * Logika untuk menilai tes berbasis skor (PG & Esai).
     */
    protected function evaluateNumericTest(Test $test, TestResult $testResult)
    {
        $pgCorrectCount = 0;
        $essayTotalScore = 0;
        $maxEssayScorePerQuestion = 10;
        $pgQuestionCount = $test->questions->where('type', 'pilihan_ganda')->count();
        $essayQuestionCount = $test->questions->where('type', 'esai')->count();

        // Proses soal PG
        $pgAnswers = $testResult->answers()->whereHas('question', fn($q) => $q->where('type', 'pilihan_ganda'))->with('question.choices')->get();
        foreach ($pgAnswers as $answer) {
            $correctChoice = $answer->question->choices->firstWhere('is_correct', true);
            if ($correctChoice && $answer->choice_id == $correctChoice->id) {
                $pgCorrectCount++;
            }
        }
        
        // Proses soal esai
        $essayQuestions = $test->questions->where('type', 'esai');
        foreach($essayQuestions as $question) {
            $answer = $testResult->answers->firstWhere('question_id', $question->id);
            $userEssayAnswer = $answer->essay_answer ?? null;
            $aiScore = 0;
            $aiFeedback = '';

            if (!empty($userEssayAnswer)) {
                $prompt = "Anda adalah penilai ahli. Berdasarkan soal dan rubrik ini, berikan skor dan umpan balik untuk jawaban siswa.\n\nSOAL:\n{$question->question_text}\n\nRUBRIK:\n{$question->rubric}\n\nJAWABAN:\n{$userEssayAnswer}\n\nFormat balasan HARUS JSON: {\"skor\": <skor_1-10>, \"umpan_balik\": \"<teks>\"}";
                $aiResponseText = Gemini::generativeModel('gemini-1.5-flash')->generateContent($prompt)->text();
                $jsonString = substr($aiResponseText, strpos($aiResponseText, '{'), strrpos($aiResponseText, '}') - strpos($aiResponseText, '{') + 1);
                $aiResult = json_decode($jsonString, true);
                
                $aiScore = $aiResult['skor'] ?? 0;
                $aiFeedback = $aiResult['umpan_balik'] ?? 'AI tidak dapat memberikan umpan balik.';
            } else {
                $prompt = "Anda adalah guru ahli. Berdasarkan soal dan rubrik, berikan CONTOH JAWABAN IDEAL.\n\nSOAL:\n{$question->question_text}\n\nRUBRIK:\n{$question->rubric}";
                $aiFeedback = Gemini::generativeModel('gemini-1.5-flash')->generateContent($prompt)->text();
            }
            TestAnswer::updateOrCreate(
                ['test_result_id' => $testResult->id, 'question_id' => $question->id],
                ['essay_answer' => $userEssayAnswer, 'ai_feedback' => $aiFeedback, 'ai_score' => $aiScore]
            );
            $essayTotalScore += $aiScore;
        }

        $pgWeight = $test->pg_weight / 100;
        $essayWeight = $test->essay_weight / 100;
        $pgScore = ($pgQuestionCount > 0) ? ($pgCorrectCount / $pgQuestionCount) * 100 : 0;
        $maxTotalEssayScore = $essayQuestionCount * $maxEssayScorePerQuestion;
        $essayScore = ($maxTotalEssayScore > 0) ? ($essayTotalScore / $maxTotalEssayScore) * 100 : 0;
        $finalScore = round(($pgScore * $pgWeight) + ($essayScore * $essayWeight));

        $testResult->update(['score' => $finalScore, 'correct_answers_count' => $pgCorrectCount]);
    }

    /**
     * Logika untuk mengevaluasi tes deskriptif (MBTI, Gaya Belajar, dll).
     */
    protected function evaluateDescriptiveTest(Test $test, TestResult $testResult)
    {
        // Muat relasi yang diperlukan
        $answers = $testResult->answers()->whereNotNull('choice_id')->with('choice')->get();

        // 1. Hitung total poin untuk setiap dimensi
        $dimensionScores = [];
        foreach ($answers as $answer) {
            if ($answer->choice && $answer->choice->dimension) {
                $dimension = $answer->choice->dimension;
                $points = $answer->choice->points;
                $dimensionScores[$dimension] = ($dimensionScores[$dimension] ?? 0) + $points;
            }
        }
        
        // 2. Baca "aturan main" dari database
        $outcome = '';
        $dimensionPairsString = $test->dimension_pairs;

        if (!$dimensionPairsString) {
            // Jika aturan main kosong, hentikan dan beri hasil default
            $testResult->update(['descriptive_outcome' => 'TIDAK_TERDEFINISI']);
            return;
        }

        if (str_contains($dimensionPairsString, ' ')) {
            // Skenario 1: Tes berpasangan seperti MBTI (dipisahkan spasi)
            $pairs = explode(' ', $dimensionPairsString); // -> ['E,I', 'S,N', 'T,F', 'J,P']
            foreach ($pairs as $pairString) {
                $pair = explode(',', $pairString); // -> ['E', 'I']
                if (count($pair) === 2) {
                    $score1 = $dimensionScores[trim($pair[0])] ?? 0;
                    $score2 = $dimensionScores[trim($pair[1])] ?? 0;
                    $outcome .= ($score1 >= $score2) ? trim($pair[0]) : trim($pair[1]);
                }
            }
        } else {
            // Skenario 2: Tes kompetitif seperti Gaya Belajar (dipisahkan koma)
            $dimensions = explode(',', $dimensionPairsString);
            $highestScore = -1;
            $dominantDimension = '';
            foreach ($dimensions as $dimension) {
                $trimmedDimension = trim($dimension);
                $score = $dimensionScores[$trimmedDimension] ?? 0;
                if ($score > $highestScore) {
                    $highestScore = $score;
                    $dominantDimension = $trimmedDimension;
                }
            }
            $outcome = $dominantDimension;
        }

        $testResult->update(['descriptive_outcome' => $outcome]);
    }

    /**
     * Membuat gambar kartu skor untuk dibagikan menggunakan ApiFlash.
     */
    protected function generateShareImage(TestResult $testResult)
    {
        // 1. Dapatkan URL publik dari halaman hasil yang ingin di-screenshot
        $urlToScreenshot = route('test.share', $testResult);

        // 2. Siapkan parameter untuk ApiFlash
        $accessKey = config('services.apiflash.access_key');
        if (!$accessKey) {
            // Jika API key tidak ada, lewati proses ini untuk mencegah error.
            return;
        }

        try {
            // 3. Panggil API ApiFlash untuk mengambil screenshot
            $response = Http::timeout(30)->get('https://api.apiflash.com/v1/urltoimage', [
                'access_key' => $accessKey,
                'url' => $urlToScreenshot,
                'format' => 'png',
                'width' => 1200,    // Ukuran standar untuk Open Graph
                'height' => 630,
                'response_type' => 'binary' // Minta gambar sebagai data, bukan URL
            ]);

            // 4. Jika berhasil, simpan gambar ke storage lokal kita
            if ($response->successful()) {
                $imagePath = 'share-images/' . $testResult->share_uuid . '.png';
                
                // Buat folder jika belum ada
                if (!Storage::disk('public')->exists('share-images')) {
                    Storage::disk('public')->makeDirectory('share-images');
                }

                // Simpan konten gambar (body) ke file
                Storage::disk('public')->put($imagePath, $response->body());

                // Update path gambar di database
                $testResult->update(['share_image_path' => $imagePath]);
            }
        } catch (\Exception $e) {
            // Jika terjadi error saat memanggil API (misal: timeout),
            // catat error tersebut dan lewati proses pembuatan gambar.
            report($e);
        }
    }

    /**
     * Menampilkan halaman hasil ujian.
     */
    public function result(TestResult $testResult)
    {
        if ((int) $testResult->user_id !== Auth::id()) { abort(404); }
        if (is_null($testResult->share_uuid)) { $testResult->share_uuid = Str::uuid(); $testResult->save(); }
        $testResult->load('test');

        if ($testResult->test->result_type === 'descriptive') {
            $outcome = $testResult->test->outcomes()->where('outcome_code', $testResult->descriptive_outcome)->first();
            return view('test.result_descriptive', [
                'testResult' => $testResult,
                'outcome' => $outcome,
            ]);
        } else {
            $testResult->load('answers.question');
            $essayAnswers = $testResult->answers->filter(fn($a) => $a->question && $a->question->type === 'esai');
            $totalEssayScore = $essayAnswers->sum('ai_score');
            $essayCount = $essayAnswers->count();
            $averageEssayScore = ($essayCount > 0) ? round($totalEssayScore / $essayCount, 1) : 0; 
            return view('test.result', [
                'testResult' => $testResult,
                'averageEssayScore' => $averageEssayScore,
                'essayCount' => $essayCount,
            ]);
        }
    }
    
    /**
     * Menampilkan halaman hasil yang bisa dibagikan secara publik.
     */
    public function shareableResult(TestResult $testResult)
    {
        $testResult->load('user', 'test');
        return view('test.share', ['result' => $testResult]);
    }
}
