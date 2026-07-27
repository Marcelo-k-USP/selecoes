<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFuncoesTableAndAlterUserProgramaTable extends Migration
{
    public function up()
    {
        Schema::create('funcoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('grupo');
            $table->integer('peso')->default(0);
            $table->timestamps();
        });

        $agora = now();
        DB::table('funcoes')->insert([
            ['nome' => 'Docentes do Programa', 'grupo' => 'Docentes do Programa', 'peso' => 1, 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Secretários(as) do Programa', 'grupo' => 'Secretários(as) do Programa', 'peso' => 2, 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Coordenadores(as) do Programa', 'grupo' => 'Coordenadores(as) do Programa', 'peso' => 3, 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Serviço de Pós-Graduação', 'grupo' => 'Funcionários(as) do Setor', 'peso' => 4, 'created_at' => $agora, 'updated_at' => $agora],
            ['nome' => 'Coordenadores(as) da Pós-Graduação', 'grupo' => 'Coordenadores(as) do Setor', 'peso' => 5, 'created_at' => $agora, 'updated_at' => $agora],
        ]);

        Schema::rename('user_programa', 'user_funcao');

        Schema::table('user_funcao', function (Blueprint $table) {
            $table->unsignedBigInteger('funcao_id')->after('programa_id')->nullable();
        });

        foreach (DB::table('funcoes')->get() as $funcao)
            DB::table('user_funcao')->where('funcao', $funcao->nome)->update(['funcao_id' => $funcao->id]);

        Schema::table('user_funcao', function (Blueprint $table) {
            $table->dropColumn('funcao');
            $table->foreign('funcao_id')->references('id')->on('funcoes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('user_funcao', function (Blueprint $table) {
            $table->string('funcao')->after('programa_id')->nullable();
        });

        foreach (DB::table('funcoes')->get() as $funcao)
            DB::table('user_funcao')->where('funcao_id', $funcao->id)->update(['funcao' => $funcao->nome]);

        Schema::table('user_funcao', function (Blueprint $table) {
            $table->string('funcao')->nullable(false)->change();
            $table->dropForeign(['funcao_id']);
            $table->dropColumn('funcao_id');
        });

        Schema::rename('user_funcao', 'user_programa');

        Schema::dropIfExists('funcoes');
    }
}
