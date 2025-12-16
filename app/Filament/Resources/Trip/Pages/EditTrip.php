<?php

namespace App\Filament\Resources\Trip\Pages;

use App\Filament\Resources\Trip\TripResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrip extends EditRecord
{
    protected static string $resource = TripResource::class;
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['surat_jalan_ids'] = $this->record->suratJalan()->pluck('id')->toArray();
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newSuratJalanIds = $data['surat_jalan_ids'] ?? [];
        unset($data['surat_jalan_ids']);
        unset($data['preview_total_berat']);
        
        $this->newSuratJalanIds = $newSuratJalanIds;
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        $currentIds = $this->record->suratJalan()->pluck('id')->toArray();
        $newIds = $this->newSuratJalanIds ?? [];
        
        $affectedPesananIds = collect();
        
        // ✅ Remove SJ from trip
        $removedIds = array_diff($currentIds, $newIds);
        if (!empty($removedIds)) {
            $removedSJ = \App\Models\SuratJalan::whereIn('id', $removedIds)->get();
            $affectedPesananIds = $affectedPesananIds->merge($removedSJ->pluck('pesanan_id'));
            
            \App\Models\SuratJalan::whereIn('id', $removedIds)
                ->update([
                    'trip_id' => null,
                    'status' => 'draft',
                    'tanggal_kirim' => null,
                ]);
        }
        
        // ✅ Add SJ to trip - BUT DON'T change status yet!
        $addedIds = array_diff($newIds, $currentIds);
        if (!empty($addedIds)) {
            $addedSJ = \App\Models\SuratJalan::whereIn('id', $addedIds)->get();
            $affectedPesananIds = $affectedPesananIds->merge($addedSJ->pluck('pesanan_id'));
            
            \App\Models\SuratJalan::whereIn('id', $addedIds)
                ->update([
                    'trip_id' => $this->record->id,
                    // ⚠️ DON'T change status if trip is still 'draft'
                    // Status will change when trip status becomes 'berangkat'
                ]);
            
            // ✅ BUT if trip already 'berangkat', update new SJ to 'dikirim'
            if ($this->record->status === 'berangkat') {
                \App\Models\SuratJalan::whereIn('id', $addedIds)
                    ->update([
                        'status' => 'dikirim',
                        'tanggal_kirim' => $this->record->tanggal_trip,
                    ]);
            }
        }
        
        // ✅ Update tanggal_kirim if trip date changed (only for 'dikirim' SJ)
        if (!empty($newIds)) {
            $existingSJ = \App\Models\SuratJalan::whereIn('id', $newIds)->get();
            $affectedPesananIds = $affectedPesananIds->merge($existingSJ->pluck('pesanan_id'));
            
            \App\Models\SuratJalan::whereIn('id', $newIds)
                ->where('trip_id', $this->record->id)
                ->where('status', 'dikirim') // Only update if already dikirim
                ->update([
                    'tanggal_kirim' => $this->record->tanggal_trip,
                ]);
        }
        
        // ✅ Update all affected pesanan
        foreach ($affectedPesananIds->unique() as $pesananId) {
            $this->updatePesananStatus($pesananId);
        }
    }
    
    private function updatePesananStatus(int $pesananId): void
    {
        $pesanan = \App\Models\Pesanan::with('suratJalan')->find($pesananId);
        
        if (!$pesanan) {
            return;
        }
        
        $allSuratJalan = $pesanan->suratJalan;
        
        if ($allSuratJalan->isEmpty()) {
            $pesanan->update(['status' => 'draft']);
            return;
        }
        
        $allReceived = $allSuratJalan->every(fn($sj) => $sj->status === 'diterima');
        $anyInDelivery = $allSuratJalan->contains(fn($sj) => in_array($sj->status, ['dikirim', 'diterima']));
        
        if ($allReceived) {
            $pesanan->update(['status' => 'selesai']);
        } elseif ($anyInDelivery) {
            $pesanan->update(['status' => 'dalam_perjalanan']);
        } else {
            $pesanan->update(['status' => 'draft']);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Trip?')
                ->modalDescription(function () {
                    $jumlahSJ = $this->record->suratJalan->count();
                    if ($jumlahSJ > 0) {
                        return "Trip ini memiliki {$jumlahSJ} Surat Jalan. Semua Surat Jalan akan dikembalikan ke status draft.";
                    }
                    return 'Apakah Anda yakin ingin menghapus trip ini?';
                })
                ->modalSubmitActionLabel('Ya, Hapus Trip')
                ->before(function () {
                    $affectedPesananIds = $this->record->suratJalan->pluck('pesanan_id')->unique();
                    
                    \App\Models\SuratJalan::where('trip_id', $this->record->id)
                        ->update([
                            'trip_id' => null,
                            'status' => 'draft',
                            'tanggal_kirim' => null,
                        ]);
                    
                    foreach ($affectedPesananIds as $pesananId) {
                        $this->updatePesananStatus($pesananId);
                    }
                })
                ->successNotificationTitle('Trip berhasil dihapus')
                ->after(function () {
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Surat Jalan Dikembalikan')
                        ->body('Semua Surat Jalan telah dikembalikan ke status draft.')
                        ->send();
                }),
            
            ForceDeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Permanen Trip?')
                ->modalDescription('Data akan dihapus permanen dan tidak dapat dikembalikan!')
                ->modalSubmitActionLabel('Ya, Hapus Permanen')
                ->before(function () {
                    $affectedPesananIds = \App\Models\SuratJalan::withTrashed()
                        ->where('trip_id', $this->record->id)
                        ->pluck('pesanan_id')
                        ->unique();
                    
                    \App\Models\SuratJalan::withTrashed()
                        ->where('trip_id', $this->record->id)
                        ->update([
                            'trip_id' => null,
                            'status' => 'draft',
                            'tanggal_kirim' => null,
                        ]);
                    
                    foreach ($affectedPesananIds as $pesananId) {
                        $this->updatePesananStatus($pesananId);
                    }
                }),
            
            RestoreAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}