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
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class RoutineResource extends Resource
{
    protected static ?string $model = Routine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Entrenamiento';
    protected static ?string $navigationLabel = 'Rutinas';

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
                Tables\Columns\TextColumn::make('user.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nombre de Rutina')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Creada')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('downloadPdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Routine $record) {
                        $pdf = Pdf::loadView('pdf.routine', ['routine' => $record]);

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "rutina-{$record->id}.pdf"
                        );
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
            'index' => Pages\ListRoutines::route('/'),
            'create' => Pages\CreateRoutine::route('/create'),
            'edit' => Pages\EditRoutine::route('/{record}/edit'),
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
}
