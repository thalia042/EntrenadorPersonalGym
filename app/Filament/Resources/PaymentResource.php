<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
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
