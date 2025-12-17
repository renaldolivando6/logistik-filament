<?php
// ==========================================
// EditUser.php
// ==========================================
namespace App\Filament\Resources\User\Pages;

use App\Filament\Resources\User\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove password from fill data
        unset($data['password']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only hash password if it's being changed
        if (filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}