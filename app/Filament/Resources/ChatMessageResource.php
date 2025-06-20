<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatMessageResource\Pages;
use App\Models\ChatMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatMessageResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Pengaturan'; // Atau grup lain yang Anda inginkan

    // Kita tidak perlu form yang bisa diedit, hanya untuk melihat
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled(),
                Forms\Components\Textarea::make('message')
                    ->disabled()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('message')
                    ->label('Isi Pesan')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Kirim')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Kita tidak perlu tombol edit per baris, cukup hapus
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Ini adalah aksi untuk menghapus banyak pesan sekaligus
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatMessages::route('/'),
            // Kita hapus halaman create dan edit karena pesan dibuat dari halaman chat
            // 'create' => Pages\CreateChatMessage::route('/create'),
            // 'edit' => Pages\EditChatMessage::route('/{record}/edit'),
        ];
    }    
}