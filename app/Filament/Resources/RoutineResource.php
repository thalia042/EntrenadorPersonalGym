<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoutineResource\Pages;
use App\Filament\Resources\RoutineResource\RelationManagers;
use App\Models\Routine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoutineResource extends Resource
{
    protected static ?string $model = Routine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Cliente')
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Título de la Rutina')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción / Notas Generales'),
                ])->columns(2),

                Forms\Components\Section::make('Ejercicios de la Rutina')
                    ->schema([
                        Forms\Components\Repeater::make('routineExercises')
                            ->relationship('routineExercises')
                            ->schema([
                                Forms\Components\Select::make('exercise_id')
                                    ->relationship('exercise', 'name')
                                    ->label('Ejercicio')
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('day_of_week')
                                    ->label('Día / Bloque')
                                    ->placeholder('Ej: Lunes')
                                    ->required(),
                                Forms\Components\TextInput::make('sets')
                                    ->label('Series')
                                    ->numeric()
                                    ->default(4)
                                    ->required(),
                                Forms\Components\TextInput::make('reps')
                                    ->label('Reps')
                                    ->default('10-12')
                                    ->required(),
                                Forms\Components\TextInput::make('notes')
                                    ->label('Notas')
                                    ->placeholder('Ej: Peso progresivo')
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Ejercicio'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutines::route('/'),
            'create' => Pages\CreateRoutine::route('/create'),
            'edit' => Pages\EditRoutine::route('/{record}/edit'),
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
}
