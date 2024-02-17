<?php

namespace Digitaliseme\Core\Contracts;

interface Authenticatable
{
    public function authIdentifier(): int|string;
}
