<?php

namespace App\Filament\Pages;

use App\Facades\Bluesky;
use App\Models\Group;
use Filament\Pages\Page;

class Messages extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.messages';
    protected Group $group;

    public $selectedConversation;

    public static function getSlug(): string
    {
        return 'messages/{id?}';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return request()->attributes->get('group') && request()->attributes->get('group')->isBskyConnected();
    }

    public static function getNavigationBadge(): ?string
    {
        if (static::shouldRegisterNavigation()) {
            return Bluesky::getConversations(request()->attributes->get('group'))->count();
        } else {
            return null;
        }
    }

    public function mount(?string $id = null): void
    {
        $this->group = request()->attributes->get('group');

        if ($id) {
            $this->selectedConversation = $id;
        }
    }
}
