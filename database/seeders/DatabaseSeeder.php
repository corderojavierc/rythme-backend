<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'username' => 'javi',
            'name' => 'javi',
            'second_name' => 'cordero',
            'email' => 'javi@gmail.com',
            'password' => 'javi123',
            'profile_image' => 'https://api.dicebear.com/9.x/thumbs/svg?seed=javi',
        ]);
    }
}
