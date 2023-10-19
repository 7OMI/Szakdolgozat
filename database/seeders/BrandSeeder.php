<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert(
            array_map(function($row) { return array_combine([
                'code',
                'name',
                'properties',
            ] , $row); }, [
                ['ASM', 'Asmodee', null],
                ['DIC', 'Dickie Toys', null],
                ['EIC', 'Eichhorn', null],
                ['FAK', 'Fakopáncs', null],
                ['FUN', 'FunTime', null],
                ['FXM', 'FoxMind', null],
                ['GIG', 'Gigamic', null],
                ['GMK', 'Gém Klub', null],
                ['HCH', 'HUCH & friends', null],
                ['HER', 'HEROS', null],
                ['HPY', 'Hanky Panky Toys', null],
                ['KDR', 'Koala Dream', null],
                ['KUE', 'Kuenen', null],
                ['LIB', 'Libellud', null],
                ['LOG', 'LOGICO', null],
                ['LUK', 'LÜK', null],
                ['MGC', 'Megcos', null],
                ['NOR', 'Noris', null],
                ['ORC', 'ORCHARD TOYS', null],
                ['PIK', 'Piatnik', null],
                ['PNO', 'Peppino', null],
                ['POP', 'Popular Playthings', null],
                ['RAV', 'Ravensburger', null],
                ['SIM', 'Simba Toys', null],
                ['SMC', 'Smart Cards', null],
                ['SMG', 'Smart Games', null],
                ['SSP', 'SentoSphére', null],
                ['THF', 'Thinkfun', null],
                ['VIG', 'VIGA', null],
                ['ABA', 'ABACUSSPIELE', null],
                ['BOG', 'Blue Orange Games', null],
                ['KEM', 'Keller & Mayer', null],
                ['VOI', 'Voila', null],
                ['SUN', 'Sunny Games', null],
                ['SMX', 'Smartmax', null],
                ['MAJ', 'Majorette', null],
                ['WES', 'WESCO', null],
                ['GON', 'GONGE', null],
                ['KSK', 'K\'s Kids', null],
                ['BRG', 'Brain Games', null],
                ['LIL', 'Lilliputiens', null],
                ['BIG', 'BIG', null],
                ['HSB', 'Hasbro', null],
                ['SES', 'SES', null],
                ['LEG', 'LEGO', null],
                ['HAM', 'Hama', null],
                ['QUE', 'Quercetti', null],
                ['ICO', 'ICO', null],
                ['GOK', 'Goki', null],
                ['DJC', 'Djeco', null],
                ['HRL', 'Herlitz', null],
                ['WAD', 'WADER', null],
                ['SPI', 'Spielstabil', null],
                ['JEG', 'Jegro', null],
                ['INV', 'Invicta Education', '{"website": "https://www.invictaeducationshop.com"}'],
            ])
        );
    }
}
