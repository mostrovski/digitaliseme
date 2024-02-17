<?php

namespace Digitaliseme\Core\Contracts;

use PDO;

interface Connection
{
    public function handler(): PDO;
}
