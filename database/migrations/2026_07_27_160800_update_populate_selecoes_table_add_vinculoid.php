<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePopulateSelecoesTableAddVinculoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selecoes', function (Blueprint $table) {
            $table->foreignId('vinculo_id')->nullable()->after('estado')->constrained('vinculos');
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id) {
            DB::table('selecoes')->update(['vinculo_id' => $vinculo_pos_graduacao_id]);

            Schema::table('selecoes', function (Blueprint $table) {
                $table->unsignedBigInteger('vinculo_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selecoes', function (Blueprint $table) {
            $table->dropForeign(['vinculo_id']);
            $table->dropColumn('vinculo_id');
        });
    }
}
