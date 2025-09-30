<?php

namespace Hettiger\Honeypot\Capabilities;

use Hettiger\Honeypot\FormToken;

use function Hettiger\Honeypot\config;

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
