<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * El nombre y firma del comando en la terminal.
     */
    protected $signature = 'payments:send-reminders';

    /**
     * La descripción del comando.
     */
    protected $description = 'Envía notificaciones automáticas de cuotas por vencer en los próximos 3 días';

    public function handle(): int
    {
        $upcomingPayments = Payment::where('status', 'pending')
            ->whereDate('due_date', '<=', Carbon::now()->addDays(3))
            ->whereDate('due_date', '>=', Carbon::now())
            ->get();

        $count = 0;

        foreach ($upcomingPayments as $payment) {
            if ($payment->user) {
                Notification::make()
                    ->title('¡Tu cuota está próxima a vencer!')
                    ->body("Recuerda que tu cuota de $" . number_format($payment->amount, 2) . " vence el {$payment->due_date->format('d/m/Y')}.")
                    ->warning()
                    ->sendToDatabase($payment->user);

                $count++;
            }
        }

        $this->info("Se enviaron {$count} recordatorios de pago correctamente.");

        return Command::SUCCESS;
    }
}
