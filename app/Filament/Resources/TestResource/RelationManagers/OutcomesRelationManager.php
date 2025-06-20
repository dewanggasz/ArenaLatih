<?php

namespace App\Filament\Resources\TestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// --- TAMBAHKAN USE STATEMENT INI ---
use Filament\Tables\Actions\Action;
use App\Imports\TestOutcomesImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
// --- AKHIR USE STATEMENT ---

class OutcomesRelationManager extends RelationManager
{
    protected static string $relationship = 'outcomes';
    protected static ?string $title = 'Kemungkinan Hasil Akhir (untuk Tes Deskriptif)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('outcome_code')
                    ->label('Kode Hasil (Contoh: INTJ)')
                    ->required()->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->label('Judul Hasil (Contoh: Sang Arsitek)')
                    ->required()->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi Lengkap Tipe Hasil')
                    ->required()->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('outcome_code')->label('Kode'),
                Tables\Columns\TextColumn::make('title')->label('Judul'),
            ])
            // --- PERUBAHAN DI SINI: MENAMBAHKAN HEADER ACTIONS ---
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Action::make('importOutcomes')
                    ->label('Impor Hasil dari CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Upload File CSV')
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
                            ->disk('local')
                    ])
                    ->action(function (array $data) {
                        try {
                            $testId = $this->getOwnerRecord()->id;
                            Excel::import(new TestOutcomesImport($testId), $data['attachment']);
                            Notification::make()
                                ->title('Data Hasil Berhasil Diimpor')
                                ->success()
                                ->send();
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];
                            foreach ($failures as $failure) {
                                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                            }
                            Notification::make()
                                ->title('Terjadi Kesalahan Validasi Saat Impor')
                                ->body(implode("\n", $errorMessages))
                                ->danger()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Terjadi Kesalahan Umum')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            // --- AKHIR PERUBAHAN ---
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

                        ->bulkActions([
                // Ini adalah aksi untuk menghapus banyak pesan sekaligus
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
