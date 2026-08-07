<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateParametrosAndCategoriasTablesMoveCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('processos')->nullable()->after('exige_disciplinas');
            $table->integer('max_disciplinas')->nullable()->after('processos');
            $table->string('link_acompanhamento')->nullable()->after('max_disciplinas');
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id) {
            $parametro = DB::table('parametros')->where('vinculo_id', $vinculo_pos_graduacao_id)->first();
            if ($parametro)
                DB::table('categorias')->where('vinculo_id', $vinculo_pos_graduacao_id)->where('nome', 'Aluno Especial')->update([
                    'processos' => $parametro->processos_especiais,
                    'max_disciplinas' => $parametro->max_disciplinas_aluno_especial,
                    'link_acompanhamento' => $parametro->link_acompanhamento_especiais,
                ]);
            DB::table('categorias')->where('vinculo_id', $vinculo_pos_graduacao_id)->where('nome', 'Aluno Regular')->update([
                'processos' => 'Inscrição e Matrícula',
            ]);
        }

        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn([
                'link_acompanhamento_especiais',
                'processos_especiais',
                'max_disciplinas_aluno_especial'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->string('processos_especiais')->nullable();
            $table->integer('max_disciplinas_aluno_especial')->nullable();
            $table->string('link_acompanhamento_especiais')->nullable();
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id) {
            $categoria_aluno_especial = DB::table('categorias')->where('vinculo_id', $vinculo_pos_graduacao_id)->where('nome', 'Aluno Especial')->first();
            if ($categoria_aluno_especial)
                DB::table('parametros')->where('vinculo_id', $vinculo_pos_graduacao_id)->update([
                    'processos_especiais' => $categoria_aluno_especial->processos,
                    'max_disciplinas_aluno_especial' => $categoria_aluno_especial->max_disciplinas,
                    'link_acompanhamento_especiais' => $categoria_aluno_especial->link_acompanhamento,
                ]);
        }

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn([
                'processos',
                'max_disciplinas',
                'link_acompanhamento'
            ]);
        });
    }
}
