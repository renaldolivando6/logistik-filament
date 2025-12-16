<?php

namespace App\Filament\Resources\SuratJalan\Pages;

use App\Filament\Resources\SuratJalan\SuratJalanResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSuratJalan extends ViewRecord
{
    protected static string $resource = SuratJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ ACTION 1: Mark as "Diterima"
            Action::make('mark_received')
                ->label('Tandai Diterima')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Penerimaan')
                ->modalDescription('Apakah barang sudah diterima oleh pelanggan dengan kondisi baik?')
                ->modalSubmitActionLabel('Ya, Sudah Diterima')
                ->visible(fn () => $this->record->status === 'dikirim')
                ->action(function () {
                    // Update surat jalan
                    $this->record->update([
                        'status' => 'diterima',
                        'tanggal_terima' => now(),
                    ]);
                    
                    // ✅ Auto update pesanan status
                    $this->updatePesananStatus($this->record->pesanan_id);
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Surat Jalan Diterima')
                        ->body('Status berhasil diupdate. Barang telah diterima pelanggan! ✅')
                        ->send();
                    
                    // Check if pesanan is complete
                    $pesanan = $this->record->pesanan;
                    if ($pesanan && $pesanan->status === 'selesai') {
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Pesanan Selesai!')
                            ->body("Pesanan #{$pesanan->id} telah selesai karena semua surat jalan sudah diterima.")
                            ->send();
                    }
                }),
            
            // ✅ ACTION 2: Cancel Surat Jalan
            Action::make('batal')
                ->label('Batalkan Surat Jalan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Surat Jalan?')
                ->modalDescription('Surat jalan ini akan dibatalkan. Pastikan tidak ada trip yang menggunakan surat jalan ini.')
                ->modalSubmitActionLabel('Ya, Batalkan')
                ->visible(fn () => $this->record->status === 'draft' && $this->record->trip_id === null)
                ->action(function () {
                    $this->record->update(['status' => 'batal']);
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Surat Jalan Dibatalkan')
                        ->body('Status surat jalan berhasil diupdate menjadi batal.')
                        ->send();
                }),
            
            // ✅ Regular Actions
            EditAction::make()
                ->visible(fn () => $this->record->trip_id === null), // Can only edit if not in trip
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
            return;
        }
        
        // Check if all SJ are received
        $allReceived = $allSuratJalan->every(fn($sj) => $sj->status === 'diterima');
        
        // Check if any SJ is in delivery
        $anyInDelivery = $allSuratJalan->contains(fn($sj) => in_array($sj->status, ['dikirim', 'diterima']));
        
        if ($allReceived) {
            $pesanan->update(['status' => 'selesai']);
        } elseif ($anyInDelivery) {
            $pesanan->update(['status' => 'dalam_perjalanan']);
        }
    }
}