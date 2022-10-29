<?php

namespace Hettiger\Honeypot\Capabilities;

use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\FormToken;

trait InteractsWithFormTokens
{
    protected function token(): FormToken
    {
        return FormToken::fromId($this->tokenId());
    }

    protected function tokenId(): ?string
    {
        return request()->headers->get($this->tokenHeaderName());
    }

    protected function tokenHeaderName(): string
    {
        return config('header');
    }
}
