<?php

namespace App\Livewire;

use App\Data\Bluesky\Conversation;
use App\Facades\Bluesky;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ChatList extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $conversationId = null;

    public function render()
    {
        return view(
            'livewire.chat-list',
            $this->getViewData()
        );
    }

    public function getViewData()
    {
        $data = [
            'selectedConversation' => null,
            'conversations' => Bluesky::getConversations(request()->attributes->get('group'))
        ];

        if ($this->conversationId) {
            $data['selectedConversation'] = Bluesky::getConversation(request()->attributes->get('group'), $this->conversationId);
        }
        return $data;
    }
}
