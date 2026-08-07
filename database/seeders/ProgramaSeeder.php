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
        $isIntegrado = config('selecoes.integracao-cadastros-auxiliares');

        if(!$isIntegrado) {
            $programas = Posgraduacao::listarProgramas();
            foreach ($programas as $programa) {
                $nome = "{$programa['nomcur']}";

                Programa::updateOrCreate(
                    ['nome' => $nome],
                );
            }
        }
        else{
            $programas = app(ProgramasClientInterface::class)->listar();
            foreach ($programas as $programa) {
                $nome = "{$programa['nomcur']} ({$programa['codslg']})";

                Programa::updateOrCreate(
                    ['nome' => $nome],
                );
            }
        }
    }
}
