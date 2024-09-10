<?php

declare(strict_types=1);

namespace Tests;

// Load the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Dotenv\Dotenv;

abstract class TestCase extends BaseTestCase
{
    private Dotenv $dotenv;

    protected function setUp(): void
    {
        // Load environment variables
        $this->dotenv = new Dotenv();
        $this->dotenv->loadEnv(__DIR__ . '/../.env');
    }
}
