<?php

namespace App\Filament\Resources\BuybackResource\Pages;

use App\Filament\Resources\BuybackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuybacks extends ListRecords
{
    protected static string $resource = BuybackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}