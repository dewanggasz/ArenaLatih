<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Test;

class PopularTestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Paket Latihan Terpopuler';

    protected static ?int $sort = 3; // Mengatur urutan widget ini di dasbor

    public function table(Table $table): Table
    {
        return $table
            // Ambil data Test, hitung relasi 'results', dan urutkan berdasarkan jumlah tersebut
            ->query(
                Test::withCount('results')->orderBy('results_count', 'desc')
            )
            // Hanya tampilkan 5 teratas
            ->paginated(false)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Latihan'),
                
                Tables\Columns\TextColumn::make('results_count')
                    ->label('Jumlah Pengerjaan')
                    ->sortable(),
            ]);
    }
}
