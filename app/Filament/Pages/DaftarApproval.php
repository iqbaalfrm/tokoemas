<?php

namespace App\Filament\Pages;

use App\Models\Approval;
use App\Models\CashFlow; 
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
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
                
                // --- KOLOM INI YANG DIPERBAIKI ---
                TextColumn::make('approvable_type')
                    ->label('Tipe Permintaan')
                    ->formatStateUsing(function ($state, Approval $record) {
                        $modelName = class_basename($state);
                        $action = $record->changes['action'] ?? null; // Ambil action

                        if ($record->approvable_id === null) {
                            return "BUAT BARU: {$modelName}"; 
                        }
                        if ($action === 'delete') { // Cek soft delete
                            return "HAPUS SEMENTARA: {$modelName}";                             
                        }                                                                        
                        if ($action === 'force_delete') { // Cek force delete
                            return "HAPUS PERMANEN: {$modelName}";
                        }
                        // Defaultnya EDIT
                        return "EDIT DATA: {$modelName}"; 
                    })
                    ->badge()
                    ->color(fn (Approval $record): string => match (true) {
                        $record->approvable_id === null => 'info', 
                        ($record->changes['action'] ?? null) === 'delete' => 'danger', // Warna danger untuk soft delete
                        ($record->changes['action'] ?? null) === 'force_delete' => 'danger', // Warna danger untuk force delete
                        default => 'warning', // Warna warning untuk edit
                    }),
                // --- AKHIR PERBAIKAN KOLOM ---

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
                    // Sembunyikan jika request DELETE (baik soft maupun force)
                    ->hidden(fn (Approval $record) => isset($record->changes['action']) && Str::contains($record->changes['action'], 'delete')), 
                    
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    // --- MODAL HEADING DIPERBAIKI ---
                    ->modalHeading(fn (Approval $record) => match(true) {
                        $record->approvable_id === null => 'Setujui Pembuatan Data Baru?',
                        ($record->changes['action'] ?? null) === 'delete' => 'Hapus Data Ini (Sementara)?', // Teks untuk soft delete
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

    // --- FUNGSI APPROVE DIPERBAIKI ---
    public function approve(Approval $record)
    {
        $message = ''; 

        if ($record->approvable_id === null && $record->approvable_type === CashFlow::class) {
            CashFlow::create($record->changes); 
            $message = 'Data Alur Kas baru telah disetujui dan dibuat.';
        
        } else {
            $model = $record->approvable; 

            if (!$model) {
                Notification::make()->title('Data asli tidak ditemukan.')->danger()->send();
                $record->update(['status' => 'rejected', 'approved_by' => auth()->id()]);
                return;
            }

            $action = $record->changes['action'] ?? null; // Ambil action

            if ($action === 'delete') { // Handle soft delete
                $model->delete(); 
                $message = 'Data telah dihapus (sementara).';
            } 
            elseif ($action === 'force_delete') { // Handle force delete
                $model->forceDelete();
                $message = 'Data telah dihapus permanen.';
            } else { // Handle edit (default)
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
    // --- AKHIR PERBAIKAN FUNGSI APPROVE ---

    public function reject(Approval $record)
    {
        $record->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        Notification::make()->title('Permintaan ditolak.')->success()->send(); 
    }
}