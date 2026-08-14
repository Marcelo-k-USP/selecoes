<?php

namespace Database\Seeders;

use App\Models\Vinculo;
use Illuminate\Database\Seeder;

class VinculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vinculos = [
            [
                'nome' => 'Pós-Graduação',
                'exige_categoria' => true,
                'permite_programa' => true,
                'permite_taxa' => true,
                'permite_nivel' => true,
                'permite_linhapesquisa' => true,
                'permite_disciplinas' => true,
                'exige_orientador' => false,
                'processos' => 'Inscrição e Matrícula',
            ],
        ];

        // adiciona registros na tabela vinculos
        foreach ($vinculos as $vinculo)
            Vinculo::create($vinculo);
    }
}
