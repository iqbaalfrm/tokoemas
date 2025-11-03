<?php

namespace App\Filament\Resources\BuybackResource\Pages;

use App\Filament\Resources\BuybackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuyback extends EditRecord
{
    protected static string $resource = BuybackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
