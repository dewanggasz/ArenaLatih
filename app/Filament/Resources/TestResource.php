<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Filament\Resources\TestResource\RelationManagers;
use App\Models\Test;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Set; // <-- Tambahkan ini
use Illuminate\Support\Collection; // <-- Tambahkan ini

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        // Field 1: Memilih Kategori Utama
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori Utama')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->live() // Membuat form reaktif terhadap perubahan field ini
                            ->afterStateUpdated(fn (Set $set) => $set('sub_category_id', null)) // Reset sub-kategori jika ini berubah
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                if ($record && $record->sub_category_id) {
                                    $set('category_id', $record->subCategory->category_id);
                                }
                            })
                            ->dehydrated(false) // Field ini tidak disimpan ke database
                            ->required(),

                        // Field 2: Memilih Sub-Kategori (tergantung field 1)
                        Forms\Components\Select::make('sub_category_id')
                            ->label('Sub-Kategori Latihan')
                            ->options(function (Get $get): Collection {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return collect(); // Kosongkan jika Kategori Utama belum dipilih
                                }
                                return SubCategory::where('category_id', $categoryId)->pluck('name', 'id');
                            })
                            ->required()
                            ->live(), // Diperlukan agar form lain bisa bereaksi
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Paket Latihan')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Pengaturan Ujian & Penilaian')
                    ->schema([
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->required()
                            ->numeric(),
                        
                        Forms\Components\Select::make('result_type')
                            ->label('Tipe Hasil Latihan')
                            ->options([
                                'numeric' => 'Berbasis Skor (Angka 0-100)',
                                'descriptive' => 'Berbasis Kategori (MBTI, Gaya Belajar, dll)',
                            ])
                            ->default('numeric')
                            ->required()
                            ->live(),

                        // --- FIELD BARU UNTUK ATURAN MAIN ---
                        Forms\Components\TextInput::make('dimension_pairs')
                            ->label('Pasangan Dimensi')
                            ->helperText("Contoh untuk MBTI: E,I S,N T,F J,P. Pisahkan pasangan dengan spasi, dan dimensi dalam pasangan dengan koma.")
                            ->visible(fn (Get $get): bool => $get('result_type') === 'descriptive')
                            ->required(fn (Get $get): bool => $get('result_type') === 'descriptive'),
                        // --- AKHIR FIELD BARU ---

                        Forms\Components\TextInput::make('pg_weight')
                            ->label('Bobot Pilihan Ganda (%)')
                            ->numeric()->default(70)
                            ->visible(fn (Get $get): bool => $get('result_type') === 'numeric'),

                        Forms\Components\TextInput::make('essay_weight')
                            ->label('Bobot Esai (%)')
                            ->numeric()->default(30)
                            ->visible(fn (Get $get): bool => $get('result_type') === 'numeric'),
                        
                        Forms\Components\Toggle::make('show_on_leaderboard')
                            ->label('Tampilkan di Papan Peringkat')
                            ->helperText('Hanya berlaku untuk tes berbasis skor.')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subCategory.name')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('result_type')
                    ->label('Tipe Hasil')
                    ->badge(),
                Tables\Columns\IconColumn::make('show_on_leaderboard')
                    ->label('Peringkat')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        // Logika untuk menampilkan relasi sekarang dikontrol oleh EditTest.php
        // Namun, kita tetap daftarkan QuestionsRelationManager di sini untuk halaman 'Create'
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit' => Pages\EditTest::route('/{record}/edit'),
        ];
    }    
}
