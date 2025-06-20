<?php

namespace App\Imports;

use App\Models\TestOutcome;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TestOutcomesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $testId;

    // Konstruktor untuk menerima ID Tes dari Relation Manager
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
        return new TestOutcome([
            'test_id'       => $this->testId,
            'outcome_code'  => $row['outcome_code'],
            'title'         => $row['title'],
            'description'   => $row['description'],
        ]);
    }

    /**
     * Menambahkan aturan validasi untuk setiap baris di CSV.
     */
    public function rules(): array
    {
        return [
            'outcome_code' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}