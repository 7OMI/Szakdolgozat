<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert(
            array_map(function($row) { return array_combine([
                'code',
                'name',
                'role',
                'properties',
            ] , $row); }, [
                ['FAK', 'Fakopáncs', '["distributor"]', null],
                ['KAT', 'Katica', '["distributor"]', null],
                ['GEM', 'Gém Klub - Gém-ker', '["distributor"]', null],
                ['GUL', 'Gulliver Kft.', '["distributor"]', null],
                ['MBX', 'MagicBox', '["distributor"]', null],
                ['REG', 'Regio Játék', '["distributor"]', null],
                ['QUA', 'Quatro Sport', '["distributor"]', null],
                ['SIM', 'Simba Toys Hungária', '["distributor"]', null],
                ['GCF', 'Gamer Café Kft. (ComPaYa Társasjátékbolt)', '["distributor"]', null],
                ['OJU', 'Okos Játékok Üzlete', '["distributor"]', null],
                ['HOR', 'HOR Zrt.', '["distributor"]', null],
                ['KWH', 'K.W.H.', '["distributor"]', null],
                ['TIL', 'Till papír', '["distributor"]', null],
                ['KVK', 'Kis Vakond', '["distributor"]', null],
                ['KIS', 'Kisistók', '["distributor"]', null],
                ['MED', 'Meló Diák', '["distributor"]', null],
                ['SMZ', 'Somogyi Zoli', '["distributor"]', null],
                ['JTN', 'Játéktenger (Kensho Kft)', '["distributor"]', null],
                ['PNO', 'Peppino', '["distributor"]', null],
                ['DIA', 'Diafilmgyártó Kft.', '["distributor"]', null],
                ['MAK', 'Makimpex 2000 Kft.', '["distributor"]', null],
                ['SPS', 'SportSarok (Papp Sándor e.v.)', '["distributor"]', null],
                ['BUA', 'BabuArt (Contact Nkft.)', '["distributor"]', null],
                ['JTV', 'Játékvár (Regin Bt.)', '["distributor"]', null],
                ['VEK', 'Vé Kft. (Sport És Barkács)', '["distributor"]', null],
                ['TSL', 'Tessloff', '["distributor"]', null],
                ['KOO', 'Kooperatív Kft.', '["distributor"]', null],
                ['MGN', 'Magánember', '["distributor"]', null],
                ['BLS', 'BLS', '["distributor"]', null],
                ['PCL', 'PannonColor', '["distributor"]', null],
                ['GDV', 'Gondviselés Kht.', '["distributor"]', null],
                ['GON', 'Gonge', '["distributor"]', null],
                ['PRM', 'Profi-Média Kft', '["distributor"]', null],
                ['PRV', 'Provida Játék', '["distributor"]', null],
                ['KEL', 'Kelle Familia Kft.', '["distributor"]', null],
                ['EPR', 'E-Profit', '["distributor"]', null],
                ['TUD', 'Tudatos Lépés Kft. (Tudatos Szülő)', '["distributor"]', null],
                ['BOY', 'Boyonex Kft.', '["distributor"]', null],
                ['YOC', 'Yo-Core Bt.', '["distributor"]', null],
                ['POL', 'POLY-MODA Kft.', '["manufacturer", "distributor"]', null],
                ['MET', 'Metalcar Kft.', '["manufacturer", "distributor"]', null],
                ['SPI', 'Spielstabil', '["distributor"]', null],
                ['INV', 'Invicta Education', '["distributor"]', null],
                ['KUE', 'Kuenen', '["distributor"]', null],
                ['DIN', 'Dinasztia Tankönyvkiadó Kft.', '["distributor"]', null],
                ['KRA', 'Krasznár és Fiai Könyvesbolt', '["distributor"]', null],
            ])
        );
    }
}
