<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Modules\User\Models\Entity;
use Illuminate\Database\Seeder;

class EntitiesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('entities')->insert(
            [
                [
                    'name' => 'Faculdade de Ciencias Exatas e Tecnologia',
                    'acronym' => 'FACET',
                ],
                [
                    'name' => 'Faculdade de Ciencias Humanas',
                    'acronym' => 'FCH',
                ],
                [
                    'name' => 'Faculdade de Cinencias Economicas',
                    'acronym' => 'FACE',
                ]
            ]
        );
    }
}
