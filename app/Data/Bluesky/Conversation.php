<?php

namespace App\Data\Bluesky;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class Conversation extends Data
{
    #[Computed]
    public $avatar;
    #[Computed]
    public $displayName;

    public function __construct(
        public string $id,
        #[DataCollectionOf(Member::class)]
        public Collection $members,
        public Message $lastMessage,
        public int $unreadCount,
        public string $status,
        public bool $muted,
        #[DataCollectionOf(Message::class)]
        public ?Collection $messages,
    ) {
        $group = request()->attributes->get('group');
        if ($group) {
            $otherMember = $members->where('did', '<>', $group->bsky_did)->first();
            $this->avatar = $otherMember?->avatar;
            $this->displayName = $otherMember?->displayName;
        }
    }
}
