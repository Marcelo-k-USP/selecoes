<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePopulateFuncaoVinculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcao_vinculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcao_id')->constrained('funcoes');
            $table->foreignId('vinculo_id')->constrained('vinculos');
            $table->timestamps();
        });

        $agora = now();

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id) {
            $registros_pos_graduacao = [];
            foreach (DB::table('funcoes')->pluck('id') as $funcao_id)
                $registros_pos_graduacao[] = [
                    'funcao_id' => $funcao_id,
                    'vinculo_id' => $vinculo_pos_graduacao_id,
                    'created_at' => $agora,
                    'updated_at' => $agora
                ];
            if (!empty($registros_pos_graduacao))
                DB::table('funcao_vinculo')->insert($registros_pos_graduacao);
        }

        $secretarios_id = DB::table('funcoes')->insertGetId([
            'nome' => 'Secretários(as) da Pesquisa',
            'grupo' => 'Funcionários(as) do Setor',
            'peso' => 4,
            'created_at' => $agora,
            'updated_at' => $agora
        ]);
        $coordenadores_id = DB::table('funcoes')->insertGetId([
            'nome' => 'Coordenadores(as) da Pesquisa',
            'grupo' => 'Coordenadores(as) do Setor',
            'peso' => 5,
            'created_at' => $agora,
            'updated_at' => $agora
        ]);

        $vinculo_pos_doc_id = DB::table('vinculos')->where('nome', 'Pós-Doc')->value('id');
        if ($vinculo_pos_doc_id) {
            DB::table('funcao_vinculo')->insert([
                [
                    'funcao_id' => $secretarios_id,
                    'vinculo_id' => $vinculo_pos_doc_id,
                    'created_at' => $agora,
                    'updated_at' => $agora
                ],
                [
                    'funcao_id' => $coordenadores_id,
                    'vinculo_id' => $vinculo_pos_doc_id,
                    'created_at' => $agora,
                    'updated_at' => $agora
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funcao_vinculo');

        DB::table('funcoes')
            ->whereIn('nome', ['Secretários(as) da Pesquisa', 'Coordenadores(as) da Pesquisa'])
            ->delete();
    }
}
