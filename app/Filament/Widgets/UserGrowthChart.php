<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Pengguna (30 Hari Terakhir)';
    
    protected static string $color = 'info';

    protected function getData(): array
    {
        // Ambil data pendaftaran pengguna selama 30 hari terakhir
        $data = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Siapkan data untuk label (tanggal) dan dataset (jumlah pendaftar)
        $labels = $data->map(function ($item) {
            return Carbon::parse($item->date)->format('d M');
        });

        $dataset = $data->map(function ($item) {
            return $item->count;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pengguna Baru',
                    'data' => $dataset,
                    'borderColor' => '#3b82f6', // blue-500
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Jenis grafik adalah 'line' (garis)
    }
}