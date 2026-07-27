<?php

namespace Database\Seeders;

use App\Models\Funcao;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Uspdev\Replicado\Posgraduacao;

class FuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // por enquanto só preenche automaticamente os Docentes do Programa, ainda não há criação automática para as demais funções
        $programas = Posgraduacao::programas();
        $funcao_docente = Funcao::where('nome', 'Docentes do Programa')->first();

        foreach ($programas as $programa) {
            $programa_sistema = Programa::where('nome', 'LIKE', "{$programa['nomcur']}%")->first();
            if (!$programa_sistema)
                continue;

            $docentes = Posgraduacao::orientadores($programa['codare']);
            foreach ($docentes as $docente) {
                $user = User::findOrCreateFromReplicado($docente['codpes']);
                if ($user) {
                    $jaAssociado = $user->programas()->where('user_funcao.programa_id', $programa_sistema->id)->wherePivot('funcao_id', $funcao_docente->id)->exists();
                    if (!$jaAssociado)
                        $user->associarProgramaFuncao($programa_sistema->nome, 'Docentes do Programa');
                }
            }
        }
    }
}
