<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ChoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'choices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('choice_text')
                    ->label('Teks Pilihan Jawaban')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_correct')
                    ->label('Tandai sebagai Jawaban Benar'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('choice_text')
            ->columns([
                Tables\Columns\TextColumn::make('choice_text'),
                Tables\Columns\IconColumn::make('is_correct')->label('Benar?')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}