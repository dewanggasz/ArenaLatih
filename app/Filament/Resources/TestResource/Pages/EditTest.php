<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use App\Filament\Resources\TestResource\RelationManagers;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTest extends EditRecord
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * PERBAIKAN DI SINI:
     * Metode ini secara langsung mengontrol Relation Manager mana yang akan ditampilkan di halaman ini.
     * Ini adalah cara yang paling kuat dan stabil.
     */
    public function getRelationManagers(): array
    {
        // Ambil data dari latihan yang sedang dibuka
        $test = $this->getRecord();
        
        // Selalu mulai dengan menampilkan pengelola soal
        $managers = [
            RelationManagers\QuestionsRelationManager::class,
        ];
        
        // Jika tipe hasilnya adalah 'descriptive' (Berbasis Kategori)...
        if ($test->result_type === 'descriptive') {
            // ...maka tambahkan pengelola "Hasil Akhir" ke dalam daftar.
            $managers[] = RelationManagers\OutcomesRelationManager::class;
        }

        // Kembalikan daftar manajer yang sudah difilter
        return $managers;
    }
}
