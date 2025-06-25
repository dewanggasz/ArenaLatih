<?php

namespace App\Http\Controllers;

// --- KUMPULAN SEMUA USE STATEMENT YANG DIBUTUHKAN ---
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestAnswer;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    // ... method index() dan start() tidak perlu diubah ...
    public function index()
    {
        $user = Auth::user();
        
        $allUserResults = $user->testResults()->get();
        
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
            'userResults' => $allUserResults,
            'completedResults' => $completedUserResults,
            'categories' => $categories,
        ]);
    }

    public function start(Test $test)
    {
        $pgCount = $test->questions()->where('type', 'pilihan_ganda')->count();
        $essayCount = $test->questions()->where('type', 'esai')->count();
        $pgKompleksCount = $test->questions()
                            ->where('question_type', 'pilihan_ganda_kompleks')
                            ->count();
        
        return view('test.start', [
            'test' => $test,
            'pgCount' => $pgCount,
            'pgKompleksCount' => $pgKompleksCount,
            'essayCount' => $essayCount,
            'title' => "Konfirmasi Latihan: " . $test->title,
            'description' => "Siap untuk memulai latihan {$test->title}? Baca petunjuk pengerjaan sebelum mulai.",
        ]);
    }

    /**
     * Menampilkan halaman pengerjaan tes atau mode pembahasan.
     * Di sinilah progres tes dibuat jika belum ada.
     */
    public function show(Test $test)
    {
        $user = Auth::user();
        $test->load('questions.choices');

        $result = TestResult::firstOrNew(
            ['user_id' => $user->id, 'test_id' => $test->id]
        );

        if (!$result->exists) {
            $result->status = 'in_progress';
            $result->started_at = now();
            $result->questions_count = $test->questions()->count();
            $result->score = 0;
            $result->correct_answers_count = 0;
            $result->share_uuid = Str::uuid();
            $result->save();
        }

        $pgKompleksCount = $test->questions()
                               ->where('question_type', 'pilihan_ganda_kompleks')
                               ->count();

        $essayCount = $test->questions()->where('type', 'esai')->count();

        if ($result->status === 'completed') {
            $result->load('answers.question');

            // --- PERBAIKAN DI SINI ---
            $userAnswers = $result->answers
                ->whereNotNull('choice_id')
                ->groupBy('question_id')    // Menggunakan foreign key langsung, ini lebih andal
                ->map(function ($answersForQuestion) {
                    if ($answersForQuestion->count() > 1) {
                        return $answersForQuestion->pluck('choice_id');
                    }
                    return $answersForQuestion->first()->choice_id;
                });
            // --- AKHIR PERBAIKAN ---

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
                'pgKompleksCount' => $pgKompleksCount,
                'essayCount' => $essayCount,
            ]);
        }

        $elapsed = now()->diffInSeconds($result->started_at);
        $totalDuration = $test->duration_minutes * 60;
        $timeRemaining = $totalDuration - $elapsed;

        return view('test.show', [
            'test' => $test,
            'isReviewMode' => false,
            'timeRemaining' => $timeRemaining > 0 ? $timeRemaining : 0,
            'pgKompleksCount' => $pgKompleksCount,
            'essayCount' => $essayCount,
        ]);
    }
    
    public function submit(Request $request, Test $test)
    {
        $user = Auth::user();
        $testResult = TestResult::where('user_id', $user->id)
                                    ->where('test_id', $test->id)
                                    ->where('status', 'in_progress')
                                    ->firstOrFail();

        $submittedAnswers = $request->input('answers', []);
        
        foreach ($submittedAnswers as $question_id => $answerValue) {
            if (empty($answerValue)) continue;

            $question = Question::find($question_id);
            if (!$question) continue;

            if (is_array($answerValue)) {
                foreach ($answerValue as $choiceId) {
                    TestAnswer::create([
                        'test_result_id' => $testResult->id,
                        'question_id'    => $question_id,
                        'choice_id'      => $choiceId,
                    ]);
                }
            } 
            else {
                TestAnswer::create([
                    'test_result_id' => $testResult->id,
                    'question_id'    => $question_id,
                    'choice_id'      => is_numeric($answerValue) ? $answerValue : null,
                    'essay_answer'   => is_string($answerValue) && !is_numeric($answerValue) ? $answerValue : null,
                ]);
            }
        }
        
        if ($test->result_type === 'numeric') {
            $this->evaluateNumericTest($test, $testResult);
        } elseif ($test->result_type === 'descriptive') {
            $this->evaluateDescriptiveTest($test, $testResult);
        }
        
        $testResult->status = 'completed';
        $testResult->save();
        
        // $this->generateShareImage($testResult);
        return redirect()->route('test.result', ['testResult' => $testResult->id]);
    }

    protected function evaluateNumericTest(Test $test, TestResult $testResult)
    {
        $pgCorrectCount = 0;
        $essayTotalScore = 0;
        $maxEssayScorePerQuestion = 10;

        $pgQuestionCount = $test->questions()->where('type', 'pilihan_ganda')->count();
        $essayQuestionCount = $test->questions()->where('type', 'esai')->count();
        
        $pgQuestions = $test->questions()->where('type', 'pilihan_ganda')->with('choices')->get();
        $userAnswersGrouped = $testResult->answers->groupBy('question_id');

        foreach ($pgQuestions as $question) {
            $userAnswersForQuestion = $userAnswersGrouped->get($question->id);
            if (!$userAnswersForQuestion) continue;

            if ($question->question_type === 'pilihan_ganda_kompleks') {
                $correctChoiceIds = $question->choices->where('is_correct', true)->pluck('id')->sort()->values();
                $userChoiceIds = $userAnswersForQuestion->pluck('choice_id')->sort()->values();
                if ($correctChoiceIds == $userChoiceIds) {
                    $pgCorrectCount++;
                }
            } else {
                $correctChoice = $question->choices->firstWhere('is_correct', true);
                $userAnswer = $userAnswersForQuestion->first();

                if ($correctChoice && $userAnswer && $userAnswer->choice_id == $correctChoice->id) {
                    $pgCorrectCount++;
                }
            }
        }
        
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
    
    protected function evaluateDescriptiveTest(Test $test, TestResult $testResult)
    {
        $answers = $testResult->answers()->whereNotNull('choice_id')->with('choice')->get();

        $dimensionScores = [];
        foreach ($answers as $answer) {
            if ($answer->choice && $answer->choice->dimension) {
                $dimension = $answer->choice->dimension;
                $points = $answer->choice->points;
                $dimensionScores[$dimension] = ($dimensionScores[$dimension] ?? 0) + $points;
            }
        }
        
        $outcome = '';
        $dimensionPairsString = $test->dimension_pairs;

        if (!$dimensionPairsString) {
            $testResult->update(['descriptive_outcome' => 'TIDAK_TERDEFINISI']);
            return;
        }

        if (str_contains($dimensionPairsString, ' ')) {
            $pairs = explode(' ', $dimensionPairsString);
            foreach ($pairs as $pairString) {
                $pair = explode(',', $pairString);
                if (count($pair) === 2) {
                    $score1 = $dimensionScores[trim($pair[0])] ?? 0;
                    $score2 = $dimensionScores[trim($pair[1])] ?? 0;
                    $outcome .= ($score1 >= $score2) ? trim($pair[0]) : trim($pair[1]);
                }
            }
        } else {
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
    
    protected function generateShareImage(TestResult $testResult)
    {
        $urlToScreenshot = route('test.share', $testResult);
        $accessKey = config('services.apiflash.access_key');
        if (!$accessKey) {
            return;
        }

        try {
            $response = Http::timeout(30)->get('https://api.apiflash.com/v1/urltoimage', [
                'access_key' => $accessKey,
                'url' => $urlToScreenshot,
                'format' => 'png',
                'width' => 1200,
                'height' => 630,
                'response_type' => 'binary'
            ]);

            if ($response->successful()) {
                $imagePath = 'share-images/' . $testResult->share_uuid . '.png';
                
                if (!Storage::disk('public')->exists('share-images')) {
                    Storage::disk('public')->makeDirectory('share-images');
                }

                Storage::disk('public')->put($imagePath, $response->body());
                $testResult->update(['share_image_path' => $imagePath]);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
    
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
                'title' => "Hasil Latihan: {$testResult->test->title}",
                'description' => "Lihat hasil skormu untuk latihan {$testResult->test->title} dan bandingkan dengan yang lain.",
            ]);
        }
    }
    
    public function shareableResult(TestResult $testResult)
    {
        $testResult->load('user', 'test');
        return view('test.share', ['result' => $testResult]);
    }
}
