<?php

namespace App\Data\Bluesky;

use Spatie\LaravelData\Data;

class Member extends Data
{
    public function __construct(
        public string $did,
        public string $handle,
        public string $displayName,
        public string $avatar,
    ) {}
}
