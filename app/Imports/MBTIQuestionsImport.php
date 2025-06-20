<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MBTIQuestionsImport implements ToModel, WithHeadingRow
{
    protected int $testId;

    public function __construct(int $testId)
    {
        $this->testId = $testId;
    }

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // Buat pertanyaan utama
            $question = Question::create([
                'test_id'       => $this->testId,
                'type'          => 'pilihan_ganda',
                'question_text' => $row['question_text'],
            ]);

            // Buat pilihan jawaban 1
            if (!empty($row['choice_1_text']) && !empty($row['choice_1_dimension'])) {
                $question->choices()->create([
                    'choice_text' => $row['choice_1_text'],
                    'dimension'   => strtoupper($row['choice_1_dimension']),
                    'points'      => 1,
                ]);
            }

            // Buat pilihan jawaban 2
            if (!empty($row['choice_2_text']) && !empty($row['choice_2_dimension'])) {
                $question->choices()->create([
                    'choice_text' => $row['choice_2_text'],
                    'dimension'   => strtoupper($row['choice_2_dimension']),
                    'points'      => 1,
                ]);
            }

            return $question;
        });
    }
}
