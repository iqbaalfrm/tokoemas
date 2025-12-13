<?php

namespace App\Filament\Resources\GoldPurityResource\Pages;

use App\Filament\Resources\GoldPurityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoldPurity extends EditRecord
{
    protected static string $resource = GoldPurityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
