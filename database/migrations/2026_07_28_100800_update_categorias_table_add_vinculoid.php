<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriasTableAddVinculoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->foreignId('vinculo_id')->nullable()->after('id')->constrained('vinculos');
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id)
            DB::table('categorias')->update(['vinculo_id' => $vinculo_pos_graduacao_id]);

        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('vinculo_id')->nullable(false)->change();
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
            $table->dropForeign(['vinculo_id']);
            $table->dropColumn('vinculo_id');
        });
    }
}
