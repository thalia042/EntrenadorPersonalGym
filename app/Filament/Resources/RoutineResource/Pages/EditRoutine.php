<?php

namespace App\Filament\Resources\RoutineResource\Pages;

use App\Filament\Resources\RoutineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoutine extends EditRecord
{
    protected static string $resource = RoutineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
