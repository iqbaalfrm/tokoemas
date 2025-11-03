<?php

namespace App\Filament\Resources\BuybackResource\Pages;

use App\Filament\Resources\BuybackResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBuyback extends CreateRecord
{
    protected static string $resource = BuybackResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['processed_by_user_id'] = auth()->id();
        return $data;
    }
}
