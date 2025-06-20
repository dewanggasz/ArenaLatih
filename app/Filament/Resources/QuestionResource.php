<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Manajemen Ujian';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
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
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('explanation', null)),

                Forms\Components\RichEditor::make('question_text')
                    ->label('Isi Pertanyaan')
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\RichEditor::make('explanation')
                    ->label('Penjelasan / Pembahasan Jawaban')
                    ->helperText('Hanya akan tampil di mode pembahasan untuk soal Pilihan Ganda.')
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('type') === 'pilihan_ganda'),

                Forms\Components\Textarea::make('rubric')
                    ->label('Rubrik Penilaian untuk AI')
                    ->placeholder('Contoh: Berikan skor 1-10...')
                    ->columnSpanFull()
                    ->helperText('Jelaskan pada AI bagaimana cara menilai jawaban untuk soal Esai ini.')
                    ->visible(fn (Get $get) => $get('type') === 'esai'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_text'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);

            
    }

    // Pastikan method ini kosong agar tidak berkonflik dengan logika di EditQuestion.php
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
