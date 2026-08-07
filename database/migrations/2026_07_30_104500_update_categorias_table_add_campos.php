<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriasTableAddCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('exige_programa')->nullable()->after('descricao');
            $table->boolean('exige_nivel')->nullable()->after('exige_programa');
            $table->boolean('exige_linhapesquisa')->nullable()->after('exige_nivel');
            $table->boolean('exige_disciplinas')->nullable()->after('exige_linhapesquisa');
        });

        DB::table('categorias')->where('nome', 'Aluno Regular')->update([
            'exige_programa' => true,
            'exige_nivel' => true,
            'exige_linhapesquisa' => true,
            'exige_disciplinas' => false,
        ]);
        DB::table('categorias')->where('nome', 'Aluno Especial')->update([
            'exige_programa' => false,
            'exige_nivel' => false,
            'exige_linhapesquisa' => false,
            'exige_disciplinas' => true,
        ]);

        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('exige_programa')->nullable(false)->change();
            $table->boolean('exige_nivel')->nullable(false)->change();
            $table->boolean('exige_linhapesquisa')->nullable(false)->change();
            $table->boolean('exige_disciplinas')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn([
                'exige_programa',
                'exige_nivel',
                'exige_linhapesquisa',
                'exige_disciplinas',
            ]);
        });
    }
}
