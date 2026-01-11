<?php

namespace App\Filament\Resources\BuybackResource\Pages;

use App\Filament\Resources\BuybackResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBuyback extends ViewRecord
{
    protected static string $resource = BuybackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}