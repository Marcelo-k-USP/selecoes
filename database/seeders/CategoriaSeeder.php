<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Vinculo;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vinculo_posgraduacao_id = Vinculo::where('nome', 'Pós-Graduação')->value('id');

        $categorias = [
            [
                'vinculo_id' => $vinculo_posgraduacao_id,
                'nome' => 'Aluno Regular',
                'descricao' => 'Mestrado e Doutorado',
                'exige_programa' => true,
                'exige_nivel' => true,
                'exige_linhapesquisa' => true,
                'exige_disciplinas' => false,
                'processos' => 'Inscrição e Matrícula',
            ],
            [
                'vinculo_id' => $vinculo_posgraduacao_id,
                'nome' => 'Aluno Especial',
                'descricao' => 'Em disciplinas',
                'exige_programa' => false,
                'exige_nivel' => false,
                'exige_linhapesquisa' => false,
                'exige_disciplinas' => true,
                'processos' => 'Matrícula',
            ],
        ];

        // adiciona registros na tabela categorias
        foreach ($categorias as $categoria)
            Categoria::create($categoria);
    }
}
