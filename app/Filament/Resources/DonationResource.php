<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

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
                Components\Section::make('Detail Donasi')
                    ->schema([
                        Components\TextEntry::make('donator_name')->label('Nama Donatur'),
                        Components\TextEntry::make('email')->label('Email'),
                        Components\TextEntry::make('amount')->label('Jumlah')->money('IDR'),
                        Components\TextEntry::make('created_at')->label('Waktu Donasi')->dateTime(),
                    ])->columns(2),
                Components\Section::make('Pesan dari Donatur')
                    ->schema([
                        Components\TextEntry::make('message')->label(false)->markdown(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('donator_name')
                    ->label('Nama Donatur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Donasi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Tampilkan yang terbaru di atas
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
            'index' => Pages\ListDonations::route('/'),
            // Kita tidak memerlukan halaman create atau edit
        ];
    }    
}
