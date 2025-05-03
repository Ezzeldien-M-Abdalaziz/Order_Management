<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'Ezz',
            'email' => 'Ezz@example.com',
            'password' => bcrypt('password')
        ]);

        Customer::factory(20)->create();
    }
}
