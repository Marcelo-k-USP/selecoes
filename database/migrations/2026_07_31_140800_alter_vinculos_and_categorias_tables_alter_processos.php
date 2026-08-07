<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterVinculosAndCategoriasTablesAlterProcessos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // se os campos processos estiverem vazios, coloca um valor padrão, no caso, "Inscrição", para não dar erro a seguir
        DB::table('vinculos')->whereNull('processos')->update(['processos' => 'Inscrição']);
        DB::table('categorias')->whereNull('processos')->update(['processos' => 'Inscrição']);

        Schema::table('vinculos', function (Blueprint $table) {
            $table->string('processos')->nullable(false)->change();
        });
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('processos')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vinculos', function (Blueprint $table) {
            $table->string('processos')->nullable()->change();
        });
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('processos')->nullable()->change();
        });
    }
}
