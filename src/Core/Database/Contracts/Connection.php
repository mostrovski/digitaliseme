<?php

namespace Digitaliseme\Core\Database\Contracts;

use PDO;

interface Connection
{
    public function handler(): PDO;
}
