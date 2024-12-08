<?php

namespace App\Filament\GlobalAdmin\Resources\GroupResource\Pages;

use App\Actions\ConfigureGroup;
use App\Filament\GlobalAdmin\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Facades\FilamentView;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {

        $this->callHook('beforeValidate');

        $data = $this->form->getState(afterValidate: function () {
            $this->callHook('afterValidate');

            $this->callHook('beforeSave');
        });

        ConfigureGroup::run(
            data_get($data, 'domain'),
            data_get($data, 'name'),
            data_get($data, 'region'),
            data_get($data, 'description'),
            data_get($data, 'timezone'),
            data_get($data, 'bsky_url'),
            data_get($data, 'meetup_url'),
        );

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }
}
