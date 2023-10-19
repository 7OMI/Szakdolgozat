<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->insert(
            array_map(function($row) { return array_combine([
                'name',
                'properties',
            ] , $row); }, [
                ['Selejt',    '{"note": "Leselejtezendő termék"}'],
                ['Bontott',   '{"note": "Bontott termék"}'],
                ['Régi',      '{"note": "Régi (beragadt) termék"}'],
                ['Hiányos',   null],
                ['-ÚJ-',      null],
                ['Bizományi', null],
            ])
        );
    }
}
