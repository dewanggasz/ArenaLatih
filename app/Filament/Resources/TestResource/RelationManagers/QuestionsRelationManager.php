<?php

namespace App\Filament\Resources\TestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Tambahkan use statement yang dibutuhkan
use Filament\Tables\Actions\Action;
use App\Imports\QuestionsImport;
use App\Imports\MBTIQuestionsImport; // <-- Impor kelas baru kita
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Forms\Get;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        // Ambil data dari Paket Latihan "induk" untuk mengetahui tipe hasilnya
        $testResultType = $this->getOwnerRecord()->result_type;

        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipe Soal')
                    ->options([
                        'pilihan_ganda' => 'Pilihan Ganda',
                        'esai' => 'Esai',
                    ])
                    ->default('pilihan_ganda')
                    ->required()
                    ->live(),

                Forms\Components\RichEditor::make('question_text')
                    ->label('Isi Pertanyaan')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('choices')
                    ->label('Pilihan Jawaban')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('choice_text')
                            ->label('Teks Jawaban')
                            ->required(),
                        
                        // Tampilkan saklar 'Jawaban Benar' jika tes berbasis skor
                        Forms\Components\Toggle::make('is_correct')
                            ->label('Jawaban Benar')
                            ->visible(fn () => $testResultType === 'numeric'),

                        // Tampilkan field 'Dimensi' & 'Poin' jika tes berbasis kategori/deskriptif
                        Forms\Components\TextInput::make('dimension')
                            ->label('Dimensi (Contoh: E, I, S, N)')
                            ->visible(fn () => $testResultType === 'descriptive'),
                        Forms\Components\TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->default(1)
                            ->visible(fn () => $testResultType === 'descriptive'),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(4)
                    ->addActionLabel('Tambah Pilihan Jawaban')
                    ->visible(fn (Get $get): bool => $get('type') === 'pilihan_ganda'),

                Forms\Components\Textarea::make('rubric')
                    ->label('Rubrik Penilaian untuk AI')
                    ->helperText('Jelaskan pada AI bagaimana cara menilai jawaban untuk soal Esai ini.')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('type') === 'esai'),
                
                Forms\Components\RichEditor::make('explanation')
                    ->label('Penjelasan / Pembahasan Jawaban')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('type') === 'pilihan_ganda'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                Tables\Columns\TextColumn::make('question_text')->label('Isi Pertanyaan')->limit(80)->html(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge(),
                Tables\Columns\TextColumn::make('choices_count')->counts('choices')->label('Jml. Pilihan'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                
                Action::make('importNumericQuestions')
                    ->label('Impor Soal (Skor)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([ Forms\Components\FileUpload::make('attachment')->label('Gunakan Template Soal Skor')->required()->disk('local'), ])
                    ->action(function (array $data) {
                        $testId = $this->getOwnerRecord()->id;
                        try {
                            Excel::import(new QuestionsImport($testId), $data['attachment']);
                            Notification::make()->title('Soal Berhasil Diimpor')->success()->send();
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];
                            foreach ($failures as $failure) {
                                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                            }
                            Notification::make()->title('Terjadi Kesalahan Validasi')->body(implode("\n", $errorMessages))->danger()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Terjadi Kesalahan Umum')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn () => $this->getOwnerRecord()->result_type === 'numeric'),

                Action::make('importDescriptiveQuestions')
                    ->label('Impor Soal (Kepribadian)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([ Forms\Components\FileUpload::make('attachment')->label('Gunakan Template Tes Kepribadian')->required()->disk('local'), ])
                    ->action(function (array $data) {
                        $testId = $this->getOwnerRecord()->id;
                        try {
                            Excel::import(new MBTIQuestionsImport($testId), $data['attachment']);
                            Notification::make()->title('Soal Kepribadian Berhasil Diimpor')->success()->send();
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];
                            foreach ($failures as $failure) {
                                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                            }
                            Notification::make()->title('Terjadi Kesalahan Validasi')->body(implode("\n", $errorMessages))->danger()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Terjadi Kesalahan Umum')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn () => $this->getOwnerRecord()->result_type === 'descriptive'),
            ])
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
