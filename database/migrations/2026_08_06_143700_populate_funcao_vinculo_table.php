<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopulateFuncaoVinculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $agora = now();
        $funcoes_ids = DB::table('funcoes')->where('nome', 'Docentes do Programa')->pluck('id');
        $vinculo_pos_doc_id = DB::table('vinculos')->where('nome', 'Pós-Doc')->value('id');
        if ($vinculo_pos_doc_id && $funcoes_ids->isNotEmpty()) {
            $dados = [];
            foreach ($funcoes_ids as $funcao_id)
                $dados[] = [ 'funcao_id' => $funcao_id, 'vinculo_id' => $vinculo_pos_doc_id, 'created_at' => $agora, 'updated_at' => $agora ];
            DB::table('funcao_vinculo')->insert($dados);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $funcoes_ids = DB::table('funcoes')->where('nome', 'Docentes do Programa')->pluck('id');
        $vinculo_pos_doc_id = DB::table('vinculos')->where('nome', 'Pós-Doc')->value('id');
        if ($vinculo_pos_doc_id && $funcoes_ids->isNotEmpty())
            DB::table('funcao_vinculo')->whereIn('funcao_id', $funcoes_ids)->where('vinculo_id', $vinculo_pos_doc_id)->delete();
    }
}
