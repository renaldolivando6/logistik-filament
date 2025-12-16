<?php

namespace App\Filament\Resources\SuratJalan\Pages;

use App\Filament\Resources\SuratJalan\SuratJalanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratJalan extends EditRecord
{
    protected static string $resource = SuratJalanResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                // ✅ Prevent delete if already in trip
                ->before(function (DeleteAction $action) {
                    if ($this->record->trip_id) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus Surat Jalan')
                            ->body('Surat Jalan ini sudah masuk dalam Trip. Hapus dari Trip terlebih dahulu.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
            ForceDeleteAction::make()
                ->before(function (ForceDeleteAction $action) {
                    if ($this->record->trip_id) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus permanen')
                            ->body('Surat Jalan ini masih terkait dengan Trip.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
            RestoreAction::make(),
        ];
    }

    // ✅ Redirect to view if already in trip (can't edit)
    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        if ($this->record->trip_id) {
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Surat Jalan sudah masuk Trip')
                ->body('Surat Jalan yang sudah masuk Trip tidak dapat diedit. Edit melalui halaman Trip jika perlu mengubah.')
                ->send();
                
            $this->redirect(static::getResource()::getUrl('view', ['record' => $record]));
        }
    }
}