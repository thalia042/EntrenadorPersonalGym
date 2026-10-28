<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HabitLogResource\Pages;
use App\Filament\Resources\HabitLogResource\RelationManagers;
use App\Models\HabitLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class HabitLogResource extends Resource
{
    protected static ?string $model = HabitLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Seguimiento';
    protected static ?string $navigationLabel = 'Hábitos Diarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('weight')
                    ->label('Peso (kg)')
                    ->numeric()
                    ->suffix('kg'),
                Forms\Components\TextInput::make('calories')
                    ->label('Calorías')
                    ->numeric()
                    ->suffix('kcal'),
                Forms\Components\TextInput::make('water_ml')
                    ->label('Agua (ml)')
                    ->numeric()
                    ->suffix('ml'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas del Cliente')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('date')->label('Fecha')->date()->sortable(),
                Tables\Columns\TextColumn::make('weight')->label('Peso')->suffix(' kg'),
                Tables\Columns\TextColumn::make('calories')->label('Calorías')->suffix(' kcal'),
                Tables\Columns\TextColumn::make('water_ml')->label('Agua')->suffix(' ml'),
            ])
            ->defaultSort('date', 'desc')
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

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && ! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHabitLogs::route('/'),
            'create' => Pages\CreateHabitLog::route('/create'),
            'edit' => Pages\EditHabitLog::route('/{record}/edit'),
        ];
    }
}
