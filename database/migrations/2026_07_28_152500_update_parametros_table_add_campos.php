<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateParametrosTableAddCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->boolean('exige_categoria')->nullable()->after('vinculo_id');
            $table->boolean('exige_programa')->nullable()->after('exige_categoria');
            $table->boolean('permite_inscricao')->nullable()->after('exige_programa');
            $table->boolean('permite_matricula')->nullable()->after('permite_inscricao');
            $table->boolean('permite_taxa')->nullable()->after('permite_matricula');
            $table->boolean('permite_nivel')->nullable()->after('permite_taxa');
            $table->boolean('permite_linhapesquisa')->nullable()->after('permite_nivel');
            $table->boolean('permite_disciplinas')->nullable()->after('permite_linhapesquisa');
            $table->boolean('exige_orientador')->nullable()->after('permite_taxa');
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->first()->id;
        if ($vinculo_pos_graduacao_id) {
            DB::table('parametros')->where('vinculo_id', $vinculo_pos_graduacao_id)->update([
                'exige_categoria' => true,
                'exige_programa' => true,
                'permite_inscricao' => true,
                'permite_matricula' => true,
                'permite_taxa' => true,
                'permite_nivel' => true,
                'permite_linhapesquisa' => true,
                'permite_disciplinas' => true,
                'exige_orientador' => false,
            ]);
        }

        $agora = now();

        $vinculo_pos_doc_id = DB::table('vinculos')->where('nome', 'Pós-Doc')->first()->id;
        if ($vinculo_pos_doc_id) {
            DB::table('parametros')->insert([
                'vinculo_id' => $vinculo_pos_doc_id,
                'exige_categoria' => false,
                'exige_programa' => true,
                'permite_inscricao' => true,
                'permite_matricula' => false,
                'permite_taxa' => false,
                'permite_nivel' => false,
                'permite_linhapesquisa' => false,
                'permite_disciplinas' => false,
                'exige_orientador' => true,
                'email_setorresponsavel' => 'copesqip@usp.br',
                'email_secaoinformatica' => 'inforip@usp.br',
                'email_gerenciamentosite' => 'website_ip@usp.br',
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);
        }

        Schema::table('parametros', function (Blueprint $table) {
            $table->boolean('exige_categoria')->nullable(false)->change();
            $table->boolean('exige_programa')->nullable(false)->change();
            $table->boolean('permite_inscricao')->nullable(false)->change();
            $table->boolean('permite_matricula')->nullable(false)->change();
            $table->boolean('permite_taxa')->nullable(false)->change();
            $table->boolean('permite_nivel')->nullable(false)->change();
            $table->boolean('permite_linhapesquisa')->nullable(false)->change();
            $table->boolean('permite_disciplinas')->nullable(false)->change();
            $table->boolean('exige_orientador')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $vinculo_pos_doc_id = DB::table('vinculos')->where('nome', 'Pós-Doc')->first()->id;
        if ($vinculo_pos_doc_id)
            DB::table('parametros')->where('vinculo_id', $vinculo_pos_doc_id)->delete();

        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn([
                'exige_categoria',
                'exige_programa',
                'permite_inscricao',
                'permite_matricula',
                'permite_taxa',
                'permite_nivel',
                'permite_linhapesquisa',
                'permite_disciplinas',
                'exige_orientador'
            ]);
        });
    }
}
