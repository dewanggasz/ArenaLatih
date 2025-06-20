<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    

    // Kita tidak akan menggunakan form ini untuk membuat/mengedit, jadi bisa dikosongkan.
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }
    
    // Infolist digunakan untuk menampilkan detail saat 'View' di-klik.
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Detail Saran')
                    ->schema([
                        Components\TextEntry::make('user.name')->label('Pengirim'),
                        Components\TextEntry::make('user.email')->label('Email Pengirim'),
                        Components\TextEntry::make('created_at')->label('Waktu Kirim')->dateTime(),
                    ])->columns(3),
                Components\Section::make('Isi Pesan')
                    ->schema([
                        Components\TextEntry::make('subject')->label('Subjek'),
                        Components\TextEntry::make('message')->label('Pesan')->markdown(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Kirim')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Tampilkan yang terbaru di atas
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Aksi untuk melihat detail
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuggestions::route('/'),
            // Kita tidak memerlukan halaman create atau edit untuk saran
        ];
    }    
}