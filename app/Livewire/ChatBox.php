<?php

namespace App\Livewire;

use App\Facades\Bluesky;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Livewire\Component;

class ChatBox extends Component implements HasForms
{
    use InteractsWithForms;

    public $conversationId = null;
    public ?array $data = [];
    public bool $showUpload = false;
    public $group;

    public function render()
    {
        return view(
            'livewire.chat-box',
            $this->getViewData()
        );
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->group = request()->attributes->get('group');
    }

    public function sendMessage()
    {
        $state = $this->form->getState();
        Bluesky::sendMessage($this->group, $this->conversationId, $state['message']);
        $this->form->reset();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('attachments')
                    ->hiddenLabel()
                    ->multiple()
                    ->storeFileNamesIn('original_attachment_file_names')
                    ->fetchFileInformation()
                    ->disk(config('filachat.disk'))
                    ->directory(fn() => config('filachat.disk') == 's3' ? config('filachat.s3.directory') : 'attachments')
                    ->visibility(fn() => config('filachat.disk') == 's3' ? config('filachat.s3.visibility') : 'public')
                    ->panelLayout('grid')
                    ->extraAttributes([
                        'class' => 'filachat-filepond',
                    ])
                    ->visible(fn() => $this->showUpload),
                Split::make([
                    Actions::make([
                        Action::make('show_hide_upload')
                            ->hiddenLabel()
                            ->icon('heroicon-m-plus')
                            ->color('gray')
                            ->tooltip(__('Upload Files'))
                            ->action(fn() => $this->showUpload = ! $this->showUpload),
                    ])
                        ->grow(false),
                    Textarea::make('message')
                        ->hiddenLabel()
                        ->live(debounce: 500)
                        ->required(function (Get $get) {
                            if (is_array($get('attachments')) && count($get('attachments')) > 0) {
                                return false;
                            }

                            return true;
                        })
                        ->rows(1)
                        ->autosize()
                        ->grow(true),
                ])
                    ->verticallyAlignEnd(),
            ])
            ->columns('full')
            ->extraAttributes([
                'class' => 'p-1',
            ])
            ->statePath('data');
    }

    public function getViewData()
    {
        $data = [
            'selectedConversation' => null,
            'conversations' => Bluesky::getConversations($this->group)
        ];

        if ($this->conversationId) {
            $convo = Bluesky::getConversation($this->group, $this->conversationId);
            $data['selectedConversation'] = $convo;
            $data['conversationMessages'] = $convo?->messages;
        }
        return $data;
    }
}
