<?php

namespace Hettiger\Honeypot\Tests\Fakes;

use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class ResponsableFake implements Responsable
{
    public function __construct(public Response $expectedResponse)
    {
    }

    public function toResponse($request)
    {
        return $this->expectedResponse;
    }
}
