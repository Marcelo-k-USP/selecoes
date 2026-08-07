<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePopulateTipoArquivoVinculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipoarquivo_vinculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipoarquivo_id')->constrained('tiposarquivo');
            $table->foreignId('vinculo_id')->constrained('vinculos');
            $table->timestamps();
        });

        $agora = now();

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id) {
            $registros_pos_graduacao = [];
            foreach (DB::table('tiposarquivo')->pluck('id') as $tipo_id)
                $registros_pos_graduacao[] = [
                    'tipoarquivo_id' => $tipo_id,
                    'vinculo_id' => $vinculo_pos_graduacao_id,
                    'created_at' => $agora,
                    'updated_at' => $agora
                ];
            if (!empty($registros_pos_graduacao))
                DB::table('tipoarquivo_vinculo')->insert($registros_pos_graduacao);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipoarquivo_vinculo');
    }
}
