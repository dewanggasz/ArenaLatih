<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Test;

class PopularTestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Paket Latihan Terpopuler';

    protected static ?int $sort = 3; // Mengatur urutan widget ini di dasbor
    // PERBAIKAN: Menghapus 'static' dari $columnSpan
    protected int | string | array $columnSpan = 'full'; // Mengambil lebar penuh

    public function table(Table $table): Table
    {
        return $table
            // Ambil data Test, hitung relasi 'results', dan urutkan berdasarkan jumlah tersebut
            ->query(
                Test::withCount('results')->orderBy('results_count', 'desc')
            )
            // Hanya tampilkan 5 teratas
            ->paginated(true)
            ->defaultPaginationPageOption(5)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Latihan')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('results_count')
                    ->label('Jumlah Pengerjaan')
                    ->sortable(),
            ]);
    }
}
