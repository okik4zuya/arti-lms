<?php

namespace App\Console\Commands;

use App\Support\Permissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Create the admin, support and learner roles and their permissions (idempotent, safe in production)';

    public function handle(): int
    {
        foreach (Permissions::all() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        Role::findOrCreate('admin', 'web')->syncPermissions(Permissions::all());
        Role::findOrCreate('support', 'web')->syncPermissions(Permissions::SUPPORT);
        Role::findOrCreate('learner', 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Roles and permissions are up to date.');

        return self::SUCCESS;
    }
}
