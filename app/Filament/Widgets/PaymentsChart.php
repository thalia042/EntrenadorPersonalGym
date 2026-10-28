<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentsChart extends ChartWidget
{
    protected static ?string $heading = 'Evolución de Ingresos Mensuales ($)';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user ? $user->isAdmin() : false;
    }

    protected function getData(): array
    {
        $data = [];
        $months = [];

        // Historial de los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $total = Payment::where('status', 'paid')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos ($)',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1d4ed8',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
