<?php

namespace Database\Seeders;

use App\Models\Programa;
use Illuminate\Database\Seeder;
use Uspdev\CadastrosAuxiliaresClient\Contracts\ProgramasClientInterface;
use Uspdev\Replicado\Posgraduacao;

class ProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!config('selecoes.integracao-cadastros-auxiliares'))
            foreach (Posgraduacao::listarProgramas() as $programa)
                Programa::updateOrCreate(
                    ['nome' => $programa['nomcur']],
                    [ 'sigla' => mb_strtoupper(mb_substr($programa['nomcur'], 0, 3)) . $programa['codcur'],
                      'processos' => 'Inscrição' ]
                );
        else
            foreach (app(ProgramasClientInterface::class)->listar() as $programa)
                Programa::updateOrCreate(
                    ['nome' => $programa['nomcur'] . ' (' . $programa['codslg'] . ')'],
                    [ 'sigla' => mb_strtoupper(mb_substr($programa['nomcur'], 0, 3)) . $programa['codcur'],
                      'processos' => 'Inscrição' ]
                );
    }
}
