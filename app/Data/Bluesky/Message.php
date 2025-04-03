<?php

namespace App\Data\Bluesky;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class Message extends Data
{
    #[Computed]
    public bool $isSender;

    public function __construct(
        public string $id,
        #[MapName('sender.did')]
        public string $senderDid,
        public string $text,
        public Carbon $sentAt,
        public ?array $attachments
    ) {
        $group = request()->attributes->get('group');
        if ($group) {
            $this->sentAt = $sentAt->setTimezone($group->timezone);
            $this->isSender = $senderDid == $group->bsky_userdid;
        } else {
            $this->isSender = false;
        }
    }
}
