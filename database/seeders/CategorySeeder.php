<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert(
            array_map(function($row) { return array_combine([
                'code',
                'name',
                'properties',
            ] , $row); }, [
                ['Hang', 'Hangszer', null],
                ['Könyv', 'Könyv', null],
                ['Mozg', 'Mozgás', null],
                ['Kreat', 'Kreatív', null],
                ['Báb', 'Báb', null],
                ['Term', 'Természet felfedező', null],
                ['Társ', 'Társasjáték', null],
                ['Fejl', 'Fejlesztő játék', null],
                ['Log&Lük', 'LOGICO és LÜK', null],
                ['CD', 'CD, DVD, VHS', null],
                ['Homok', 'Homokozós', null],
                ['FizKem', 'Fizikai-kémiai eszközök', null],
            ])
        );
    }
}
