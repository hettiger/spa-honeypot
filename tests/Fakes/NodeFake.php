<?php

namespace Hettiger\Honeypot\Tests\Fakes;

use GraphQL\Language\AST\Node;

class NodeFake extends Node
{
    public function __construct(public $value)
    {
        parent::__construct([]);
    }
}
