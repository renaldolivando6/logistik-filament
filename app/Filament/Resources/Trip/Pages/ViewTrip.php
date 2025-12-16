<?php

namespace App\Filament\Resources\Trip\Pages;

use App\Filament\Resources\Trip\TripResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTrip extends ViewRecord
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ ACTION 1: Mark as "Berangkat"
            Action::make('berangkat')
                ->label('Tandai Berangkat')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Trip Berangkat?')
                ->modalDescription('Sopir akan mulai perjalanan dengan surat jalan yang sudah ditambahkan.')
                ->modalSubmitActionLabel('Ya, Berangkat')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    // ✅ Get trip ID for debugging
                    $tripId = $this->record->id;
                    $tanggalTrip = $this->record->tanggal_trip;
                    
                    \Log::info("Updating trip #{$tripId} to berangkat");
                    
                    // ✅ Update trip status
                    $this->record->update(['status' => 'berangkat']);
                    
                    // ✅ Get SJ before update
                    $sjBefore = \App\Models\SuratJalan::where('trip_id', $tripId)->get();
                    \Log::info("Found " . $sjBefore->count() . " surat jalan for trip #{$tripId}");
                    
                    // ✅ Update all SJ in this trip
                    $updated = \App\Models\SuratJalan::where('trip_id', $tripId)
                        ->update([
                            'status' => 'dikirim',
                            'tanggal_kirim' => $tanggalTrip,
                        ]);
                    
                    \Log::info("Updated {$updated} surat jalan records");
                    
                    // ✅ Verify update
                    $sjAfter = \App\Models\SuratJalan::where('trip_id', $tripId)->get();
                    foreach ($sjAfter as $sj) {
                        \Log::info("SJ #{$sj->id}: status={$sj->status}, tanggal_kirim={$sj->tanggal_kirim}");
                    }
                    
                    // ✅ Update affected pesanan
                    $affectedPesananIds = $this->record->suratJalan()->pluck('pesanan_id')->unique();
                    \Log::info("Updating " . $affectedPesananIds->count() . " pesanan");
                    
                    foreach ($affectedPesananIds as $pesananId) {
                        $this->updatePesananStatus($pesananId);
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Trip Berangkat')
                        ->body("Trip dan {$updated} surat jalan berhasil diupdate. Selamat jalan! 🚛")
                        ->send();
                }),
            
            // ✅ ACTION 2: Mark as "Selesai"
            Action::make('selesai')
                ->label('Tandai Selesai')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Trip Selesai?')
                ->modalDescription('Pastikan sopir sudah kembali dan semua surat jalan sudah diterima pelanggan.')
                ->modalSubmitActionLabel('Ya, Selesai')
                ->visible(fn () => in_array($this->record->status, ['draft', 'berangkat']))
                ->action(function () {
                    $this->record->update(['status' => 'selesai']);
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Trip Selesai')
                        ->body('Status trip berhasil diupdate. Sopir sudah kembali! ✅')
                        ->send();
                }),
            
            // ✅ ACTION 3: Cancel Trip
            Action::make('batal')
                ->label('Batalkan Trip')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Trip?')
                ->modalDescription(function () {
                    $jumlahSJ = $this->record->suratJalan->count();
                    if ($jumlahSJ > 0) {
                        return "Trip ini memiliki {$jumlahSJ} Surat Jalan. Semua Surat Jalan akan dikembalikan ke status draft. Trip akan tetap tersimpan dengan status 'batal'.";
                    }
                    return 'Trip ini akan dibatalkan. Trip tetap tersimpan untuk riwayat.';
                })
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->visible(fn () => in_array($this->record->status, ['draft', 'berangkat']))
                ->action(function () {
                    // ✅ Rollback all SJ to draft
                    $affectedPesananIds = $this->record->suratJalan->pluck('pesanan_id')->unique();
                    
                    \App\Models\SuratJalan::where('trip_id', $this->record->id)
                        ->update([
                            'trip_id' => null,
                            'status' => 'draft',
                            'tanggal_kirim' => null,
                        ]);
                    
                    // Update trip status
                    $this->record->update(['status' => 'batal']);
                    
                    // Update affected pesanan
                    foreach ($affectedPesananIds as $pesananId) {
                        $this->updatePesananStatus($pesananId);
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Trip Dibatalkan')
                        ->body('Trip dibatalkan dan semua Surat Jalan dikembalikan ke status draft.')
                        ->send();
                }),
            
            // ✅ Regular Actions
            EditAction::make()
                ->visible(fn () => $this->record->status === 'draft'), // Only edit when draft
        ];
    }
    
    /**
     * ✅ Helper: Update Pesanan Status
     */
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
}