<?php

namespace App\Filament\Resources\GoldPurityResource\Pages;

use App\Filament\Resources\GoldPurityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoldPurities extends ListRecords
{
    protected static string $resource = GoldPurityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
