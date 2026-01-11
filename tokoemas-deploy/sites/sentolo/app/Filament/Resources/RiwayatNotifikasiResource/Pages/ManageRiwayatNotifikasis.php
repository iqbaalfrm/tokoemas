<?php

namespace App\Filament\Resources\RiwayatNotifikasiResource\Pages;

use App\Filament\Resources\RiwayatNotifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRiwayatNotifikasis extends ManageRecords
{
    protected static string $resource = RiwayatNotifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
