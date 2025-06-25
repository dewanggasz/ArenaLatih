<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Choice;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class QuestionsImport implements ToCollection, WithHeadingRow, WithValidation, WithCustomCsvSettings
{
    protected int $testId;

    public function __construct(int $testId)
    {
        $this->testId = $testId;
    }

    /**
     * Define custom settings for reading the CSV file.
     *
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => '|'
        ];
    }

    /**
     * Process the collection of rows from the imported file.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Skip a row if the question text is empty
            if (empty($row['question_text'])) continue;

            $type = strtolower($row['type'] ?? 'pilihan_ganda');

            // Handle 'essay' type questions
            if ($type === 'esai') {
                Question::create([
                    'test_id'       => $this->testId,
                    'type'          => 'esai',
                    'question_text' => $row['question_text'],
                    'rubric'        => $row['rubric'],
                ]);
                continue; // Move to the next row
            }

            // Handle 'multiple choice' questions
            $correctChoiceKeys = str_split(strtoupper($row['correct_choice'] ?? ''));
            
            // Determine the question type (complex or regular) based on the number of correct answers
            $questionTypeForDb = count($correctChoiceKeys) > 1
                ? 'pilihan_ganda_kompleks'
                : 'pilihan_ganda_biasa';

            // Create the question record
            $question = Question::create([
                'test_id'       => $this->testId,
                'type'          => 'pilihan_ganda',
                'question_type' => $questionTypeForDb,
                'question_text' => $row['question_text'],
                'explanation'   => $row['explanation'],
            ]);

            // Map choices from the row
            $choicesMap = [
                'A' => $row['choice_a'] ?? null,
                'B' => $row['choice_b'] ?? null,
                'C' => $row['choice_c'] ?? null,
                'D' => $row['choice_d'] ?? null,
                'E' => $row['choice_e'] ?? null,
            ];

            // Create choice records for non-empty choices
            foreach ($choicesMap as $key => $choiceText) {
                if (!empty($choiceText)) {
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choiceText,
                        'is_correct'  => in_array($key, $correctChoiceKeys),
                    ]);
                }
            }
        }
    }

    /**
     * Define validation rules for each row.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:pilihan_ganda,esai',
            'question_text' => 'required|string',
        ];
    }
}
