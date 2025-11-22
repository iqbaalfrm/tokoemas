<?php

namespace App\Filament\Pages;

use App\Models\Approval;
use App\Models\CashFlow; 
use App\Models\Product; 
use App\Models\Transaction; 
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament; 
use Illuminate\Support\Str; 

class DaftarApproval extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static string $view = 'filament.pages.daftar-approval';
    protected static ?string $navigationLabel = 'Daftar Approval';
    protected static ?string $navigationGroup = 'Menejemen keuangan'; 
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Approval::query()->with('user', 'approvable', 'approver')) 
            ->defaultSort('created_at', 'desc') 
            ->columns([
                TextColumn::make('user.name')
                    ->label('Admin')
                    ->searchable(),
                
                TextColumn::make('approvable_type')
                    ->label('Tipe Permintaan')
                    ->formatStateUsing(function ($state, Approval $record) {
                        $modelName = class_basename($state);
                        $action = $record->changes['action'] ?? null;

                        if ($record->approvable_id === null) {
                            return "BUAT BARU: {$modelName}"; 
                        }
                        if ($action === 'delete') {
                            return "HAPUS SEMENTARA: {$modelName}";
                        }
                        if ($action === 'force_delete') {
                            return "HAPUS PERMANEN: {$modelName}";
                        }
                         if (Str::contains($action, 'reset_stock')) {
                            return "RESET STOK: {$modelName}";
                        }
                        return "EDIT DATA: {$modelName}"; 
                    })
                    ->badge()
                    ->color(fn (Approval $record): string => match (true) {
                        $record->approvable_id === null => 'info', 
                        ($record->changes['action'] ?? null) === 'delete' => 'danger',
                        ($record->changes['action'] ?? null) === 'force_delete' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('approvable_id')
                    ->label('Data ID')
                    ->placeholder('N/A'), 

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                
                TextColumn::make('created_at')
                    ->label('Waktu Request')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                
                TextColumn::make('approver.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('N/A'),

                TextColumn::make('approved_at')
                    ->label('Waktu Verifikasi')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('N/A'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
            ])
            ->actions([
                Action::make('view_record')
                    ->label('Lihat Detail Data')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->openUrlInNewTab()
                    ->url(function (Approval $record): string {
                        if ($record->approvable_id === null || !$record->approvable) { 
                            return '#';
                        }
                        $modelName = class_basename($record->approvable_type);
                        $resourceClass = "App\\Filament\\Resources\\" . $modelName . "Resource";
                        
                        if (!class_exists($resourceClass)) {
                            return '#';
                        }
                        
                        $panelId = Filament::getCurrentPanel()->getId();
                        
                        if ($resourceClass::hasPage('view', $panelId)) {
                            return $resourceClass::getUrl('view', ['record' => $record->approvable_id]);
                        }
                        
                        if ($resourceClass::hasPage('edit', $panelId)) {
                            return $resourceClass::getUrl('edit', ['record' => $record->approvable_id]);
                        }
                        
                        return $resourceClass::getUrl('index');
                    })
                    ->visible(fn (Approval $record): bool => $record->approvable_id !== null && $record->approvable !== null), 

                Action::make('view_changes')
                    ->label('Lihat Perubahan')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function (Approval $record) {
                        if ($record->approvable_id === null) {
                            return view('filament.pages.approval-changes-modal-create', ['record' => $record]);
                        }
                        return view('filament.pages.approval-changes-modal', ['record' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->hidden(fn (Approval $record) => isset($record->changes['action']) && Str::contains($record->changes['action'], 'delete')), 
                    
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Approval $record) => match(true) {
                        $record->approvable_id === null => 'Setujui Pembuatan Data Baru?',
                        ($record->changes['action'] ?? null) === 'delete' => 'Hapus Data Ini (Sementara)?',
                        ($record->changes['action'] ?? null) === 'force_delete' => 'Hapus Data Ini Permanen?',
                        default => 'Approve Perubahan?'
                    })
                    ->modalDescription('Tindakan ini tidak bisa dibatalkan.')
                    ->action(fn (Approval $record) => $this->approve($record))
                    ->visible(fn (Approval $record) => $record->status === 'pending'),
                
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Permintaan?')
                    ->action(fn (Approval $record) => $this->reject($record))
                    ->visible(fn (Approval $record) => $record->status === 'pending'),
            ])
            ->bulkActions([]);
    }

    public function approve(Approval $record)
    {
        if ($record->status !== 'pending') {
            Notification::make()->title('Tiket sudah diproses.')->warning()->send();
            return;
        }

        $message = 'Perubahan disetujui.';

        if ($record->approvable_id === null) {
            $modelClass = $record->approvable_type;
            
            if ($modelClass === CashFlow::class || $modelClass === Product::class || $modelClass === Transaction::class) {
                $modelClass::create($record->changes);
                $modelName = class_basename($modelClass);
                $message = "Data {$modelName} baru telah disetujui dan dibuat.";
            } else {
                Notification::make()->title('Model tidak dikenali untuk operasi CREATE.')->danger()->send();
                return;
            }
        
        } else {
            $model = $record->approvable; 

            if (!$model) {
                Notification::make()->title('Data asli tidak ditemukan.')->danger()->send();
                $record->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
                return;
            }

            $action = $record->changes['action'] ?? null;

            if ($action === 'delete') {
                $model->delete(); 
                $message = 'Data telah dihapus (sementara).';
            } 
            elseif ($action === 'force_delete') {
                $model->forceDelete();
                $message = 'Data telah dihapus permanen.';
            } 
            elseif (Str::contains($action, 'reset_stock')) {
                $model->update(['stock' => 0]); 
                $message = 'Stok produk di-reset.';
            } 
            else { 
                $model->update($record->changes);
                $message = 'Perubahan disetujui.';
            }
        }

        $record->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        Notification::make()->title($message)->success()->send();
    }
    
    public function reject(Approval $record)
    {
        if ($record->status !== 'pending') {
            Notification::make()->title('Tiket sudah diproses.')->warning()->send();
            return;
        }
        
        $record->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        Notification::make()->title('Permintaan ditolak.')->success()->send(); 
    }
}