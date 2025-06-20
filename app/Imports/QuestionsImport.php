<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class QuestionsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected int $testId;

    // Method __construct untuk menerima testId dari Relation Manager
    public function __construct(int $testId)
    {
        $this->testId = $testId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            
            // Gunakan ID dari konstruktor, bukan dari file CSV
            $currentTestId = $this->testId;

            if (strtolower($row['type']) === 'esai') {
                return Question::create([
                    'test_id'       => $currentTestId, // <-- MENGGUNAKAN ID YANG BENAR
                    'type'          => 'esai',
                    'question_text' => $row['question_text'],
                    'rubric'        => $row['rubric'],
                ]);
            } else {
                $question = Question::create([
                    'test_id'       => $currentTestId, // <-- MENGGUNAKAN ID YANG BENAR
                    'type'          => 'pilihan_ganda',
                    'question_text' => $row['question_text'],
                    'explanation'   => $row['explanation'],
                ]);

                $choices = [ 'A' => $row['choice_a'], 'B' => $row['choice_b'], 'C' => $row['choice_c'], 'D' => $row['choice_d'], ];

                foreach ($choices as $key => $choiceText) {
                    if ($choiceText) {
                        $question->choices()->create([ 'choice_text' => $choiceText, 'is_correct'  => ($key === strtoupper($row['correct_choice'])), ]);
                    }
                }
                return $question;
            }
        });
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
