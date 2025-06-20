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
use Spatie\Browsershot\Browsershot;

class TestController extends Controller
{
    /**
     * Menampilkan dashboard dengan daftar tes, riwayat, dan kategori untuk filter.
     */
    public function index()
    {
        $tests = Test::with(['subCategory.category'])
                     ->withCount('questions')
                     ->latest()
                     ->get();
        $userResults = auth()->user()->testResults()->with('test')->latest()->get();
        $completedTestIds = $userResults->pluck('test_id')->unique();
        $categories = Category::whereHas('subCategories.tests')->get();

        return view('dashboard', [
            'tests' => $tests,
            'userResults' => $userResults,
            'completedTestIds' => $completedTestIds,
            'categories' => $categories
        ]);
    }

    /**
     * Menampilkan halaman konfirmasi sebelum ujian dimulai.
     */
    public function start(Test $test)
    {
        $test->load('questions');
        $pgCount = $test->questions->where('type', 'pilihan_ganda')->count();
        $essayCount = $test->questions->where('type', 'esai')->count();
        $hasTaken = TestResult::where('user_id', Auth::id())
                              ->where('test_id', $test->id)
                              ->exists();
        if ($hasTaken) {
            return redirect()->route('test.show', $test);
        }
        return view('test.start', [
            'test' => $test,
            'pgCount' => $pgCount,
            'essayCount' => $essayCount,
        ]);
    }

    /**
     * Menampilkan halaman pengerjaan tes atau mode pembahasan.
     */
    public function show(Test $test)
    {
        $testWithQuestions = Test::with('questions.choices')->find($test->id);
        $previousResult = TestResult::where('user_id', Auth::id())
                                    ->where('test_id', $test->id)
                                    ->first();
        if ($previousResult) {
            $previousResult->load('answers.question');
            $userAnswers = $previousResult->answers->pluck('choice_id', 'question_id')->filter();
            $userEssayAnswers = $previousResult->answers->whereNotNull('essay_answer')->pluck('essay_answer', 'question_id');
            $aiFeedbacks = $previousResult->answers->whereNotNull('ai_feedback')->pluck('ai_feedback', 'question_id');
            $aiScores = $previousResult->answers->whereNotNull('ai_score')->pluck('ai_score', 'question_id');
            return view('test.show', [
                'test' => $testWithQuestions,
                'isReviewMode' => true,
                'userAnswers' => $userAnswers,
                'userEssayAnswers' => $userEssayAnswers,
                'aiFeedbacks' => $aiFeedbacks,
                'aiScores' => $aiScores,
            ]);
        } else {
            return view('test.show', [
                'test' => $testWithQuestions,
                'isReviewMode' => false,
                'userAnswers' => collect(),
                'userEssayAnswers' => collect(),
                'aiFeedbacks' => collect(),
                'aiScores' => collect(),
            ]);
        }
    }

    /**
     * Menerima jawaban, mengevaluasi, dan menyimpan hasil.
     */
    public function submit(Request $request, Test $test)
    {
        $test->load('questions');
        $submittedAnswers = $request->input('answers', []);
        $user = Auth::user();

        $testResult = TestResult::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'score' => 0,
            'questions_count' => $test->questions->count(),
            'correct_answers_count' => 0,
        ]);

        foreach ($submittedAnswers as $question_id => $answer) {
            if (!empty($answer)) {
                TestAnswer::create([
                    'test_result_id' => $testResult->id,
                    'question_id' => $question_id,
                    'choice_id' => is_numeric($answer) ? $answer : null,
                    'essay_answer' => is_string($answer) && !is_numeric($answer) ? $answer : null,
                ]);
            }
        }

        if ($test->result_type === 'numeric') {
            $this->evaluateNumericTest($test, $testResult);
        } elseif ($test->result_type === 'descriptive') {
            $this->evaluateDescriptiveTest($test, $testResult);
        }

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
     * Membuat gambar kartu skor untuk dibagikan.
     */
    protected function generateShareImage(TestResult $testResult)
    {
        $testResult->load('user', 'test');
        $html = view('test.share-image-template', ['result' => $testResult])->render();
        $imagePath = 'share-images/' . $testResult->share_uuid . '.png';
        if (!Storage::disk('public')->exists('share-images')) {
            Storage::disk('public')->makeDirectory('share-images');
        }
        Browsershot::html($html)->windowSize(600, 315)->save(Storage::path('public/' . $imagePath));
        $testResult->update(['share_image_path' => $imagePath]);
    }

    /**
     * Menampilkan halaman hasil ujian.
     */
    public function result(TestResult $testResult)
    {
        if ($testResult->user_id !== Auth::id()) { abort(403); }
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
