<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && ! $user->isAdmin()) {
            return [
                Stat::make('Mis Pagos Pendientes', Payment::where('user_id', $user->id)->where('status', 'pending')->count())
                    ->color('warning')
                    ->icon('heroicon-o-clock'),
                Stat::make('Mis Rutinas Asignadas', $user->routines()->count())
                    ->color('success')
                    ->icon('heroicon-o-rectangle-stack'),
            ];
        }

        // Métricas globales para el Entrenador / Admin
        $totalIngresosMes = Payment::where('status', 'paid')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $pagosPendientes = Payment::where('status', 'pending')->count();
        $totalClientes = User::where('role', 'client')->count();

        return [
            Stat::make('Ingresos del Mes', '$' . number_format($totalIngresosMes, 2, ',', '.'))
                ->description('Total cobrado este mes')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pagos Pendientes', $pagosPendientes)
                ->description('Clientes por cobrar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pagosPendientes > 0 ? 'warning' : 'gray'),

            Stat::make('Clientes Activos', $totalClientes)
                ->description('Alumnos registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
