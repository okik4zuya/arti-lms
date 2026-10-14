<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class Article
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly string $description,
        public readonly Carbon $date,
        public readonly array $tags,
        public readonly string $body,
    ) {}
}
