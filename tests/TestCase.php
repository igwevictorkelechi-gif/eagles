<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // The EnsureInstalled middleware gates the whole app behind the setup
    // wizard until storage/installed exists. Default every test to "installed";
    // InstallerTest removes the lock to exercise the wizard itself.
    protected function setUp(): void
    {
        parent::setUp();
        @touch(storage_path('installed'));
    }

    protected function markNotInstalled(): void
    {
        @unlink(storage_path('installed'));
    }
}
