<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TagSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(BrandSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TagSeeder::class);
    }
}
