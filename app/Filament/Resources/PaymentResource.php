<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Pagos y Cuotas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\DatePicker::make('payment_date')
                    ->label('Fecha de Pago')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Fecha de Vencimiento')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        'overdue' => 'Vencido',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && ! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('ARS')->sortable(),
                Tables\Columns\TextColumn::make('payment_date')->date(),
                Tables\Columns\TextColumn::make('due_date')->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('sendReminder')
                    ->label('Enviar Recordatorio')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->visible(function (Payment $record): bool {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();

                        return $record->status !== 'paid' && $user && $user->isAdmin();
                    })
                    ->action(function (Payment $record) {
                        Notification::make()
                            ->title('Recordatorio de Pago Pending')
                            ->body("Hola {$record->user->name}, tienes una cuota de $" . number_format($record->amount, 2) . " con vencimiento el {$record->due_date}.")
                            ->warning()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Notificación enviada')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
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
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
