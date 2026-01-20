<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create system admin (no workspace)
        $systemAdmin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'role' => 'system_admin',
            'workspace_id' => null,
        ]);

        // Create Workspace 1
        $workspace1 = Workspace::create(['name' => 'Acme Corporation']);
        
        $workspace1Admin = User::create([
            'name' => 'John Admin',
            'email' => 'john@acme.com',
            'password' => Hash::make('password'),
            'role' => 'workspace_admin',
            'workspace_id' => $workspace1->id,
        ]);

        $workspace1Member = User::create([
            'name' => 'Jane Member',
            'email' => 'jane@acme.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'workspace_id' => $workspace1->id,
        ]);

        // Create items for Workspace 1
        for ($i = 1; $i <= 7; $i++) {
            Item::create([
                'workspace_id' => $workspace1->id,
                'assigned_user_id' => $i <= 3 ? $workspace1Member->id : null,
                'title' => "Acme Task {$i}",
                'status' => $i % 2 == 0 ? 'closed' : 'open',
            ]);
        }

        // Create Workspace 2
        $workspace2 = Workspace::create(['name' => 'TechStart Inc']);
        
        $workspace2Admin = User::create([
            'name' => 'Bob Admin',
            'email' => 'bob@techstart.com',
            'password' => Hash::make('password'),
            'role' => 'workspace_admin',
            'workspace_id' => $workspace2->id,
        ]);

        $workspace2Member = User::create([
            'name' => 'Alice Member',
            'email' => 'alice@techstart.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'workspace_id' => $workspace2->id,
        ]);

        // Create items for Workspace 2
        for ($i = 1; $i <= 8; $i++) {
            Item::create([
                'workspace_id' => $workspace2->id,
                'assigned_user_id' => $i <= 4 ? $workspace2Member->id : null,
                'title' => "TechStart Project {$i}",
                'status' => $i % 3 == 0 ? 'closed' : 'open',
            ]);
        }
    }
}