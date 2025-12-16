<?php

namespace App\Filament\Resources\Trip\Pages;

use App\Filament\Resources\Trip\TripResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract surat_jalan_ids before creating trip
        $suratJalanIds = $data['surat_jalan_ids'] ?? [];
        unset($data['surat_jalan_ids']);
        unset($data['preview_total_berat']);
        
        // ✅ Always set status to 'draft' on create
        $data['status'] = 'draft';
        
        // Store for after create hook
        $this->suratJalanIds = $suratJalanIds;
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        if (empty($this->suratJalanIds)) {
            return;
        }
        
        // ✅ ONLY assign trip_id, DON'T change status yet!
        // Status will change when user clicks "Tandai Berangkat"
        \App\Models\SuratJalan::whereIn('id', $this->suratJalanIds)
            ->update([
                'trip_id' => $this->record->id,
                // status: stay 'draft'
                // tanggal_kirim: still NULL
            ]);
        
        // ❌ DON'T update pesanan status yet (still draft)
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}