<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait DatabaseMigrations
{
    use RefreshDatabase {
        RefreshDatabase::refreshDatabase as parentRefreshDatabase;
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase()
    {
        // Remove sessions table migration temporarily to prevent conflicts
        $this->excludeSessionMigration();

        $this->parentRefreshDatabase();

        // Restore sessions migration file
        $this->restoreSessionMigration();
    }

    /**
     * Temporarily rename the sessions migration file
     */
    protected function excludeSessionMigration()
    {
        $sessionsMigrationPath = database_path('migrations/2025_05_10_031542_create_sessions_table.php');
        $tempPath = database_path('migrations/2025_05_10_031542_create_sessions_table.php.bak');
        
        if (file_exists($sessionsMigrationPath)) {
            rename($sessionsMigrationPath, $tempPath);
        }
    }

    /**
     * Restore the sessions migration file
     */
    protected function restoreSessionMigration()
    {
        $sessionsMigrationPath = database_path('migrations/2025_05_10_031542_create_sessions_table.php');
        $tempPath = database_path('migrations/2025_05_10_031542_create_sessions_table.php.bak');
        
        if (file_exists($tempPath)) {
            rename($tempPath, $sessionsMigrationPath);
        }
    }
}
