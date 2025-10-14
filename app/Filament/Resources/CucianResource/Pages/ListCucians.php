<?php

namespace App\Filament\Resources\CucianResource\Pages;

use App\Filament\Resources\CucianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCucians extends ListRecords
{
    protected static string $resource = CucianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
