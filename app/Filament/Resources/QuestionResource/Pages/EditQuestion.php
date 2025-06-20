<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Filament\Resources\QuestionResource\RelationManagers;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // --- METODE KUNCI UNTUK MENAMPILKAN RELASI SECARA DINAMIS ---
    protected function getFooterWidgets(): array
    {
        // Ambil data soal yang sedang dibuka
        $question = $this->record;

        // Jika tipe soal adalah 'pilihan_ganda', maka tampilkan widget relasi 'Choices'
        if ($question->type === 'pilihan_ganda') {
            return [
                RelationManagers\ChoicesRelationManager::class,
            ];
        }

        // Jika tidak, jangan tampilkan apa-apa
        return [];
    }
}