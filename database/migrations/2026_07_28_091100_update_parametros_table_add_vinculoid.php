<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateParametrosTableAddVinculoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->foreignId('vinculo_id')->nullable()->after('id')->constrained('vinculos');
            $table->renameColumn('email_servicoposgraduacao', 'email_setorresponsavel');
            $table->string('boleto_codigo_fonte_recurso')->nullable()->change();
            $table->string('boleto_estrutura_hierarquica')->nullable()->change();
            $table->string('email_setorresponsavel')->nullable()->change();
        });

        $vinculo_pos_graduacao_id = DB::table('vinculos')->where('nome', 'Pós-Graduação')->value('id');
        if ($vinculo_pos_graduacao_id)
            DB::table('parametros')->update(['vinculo_id' => $vinculo_pos_graduacao_id]);

        Schema::table('parametros', function (Blueprint $table) {
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
        Schema::table('parametros', function (Blueprint $table) {
            $table->string('boleto_codigo_fonte_recurso')->nullable(false)->change();
            $table->string('boleto_estrutura_hierarquica')->nullable(false)->change();
            $table->string('email_setorresponsavel')->nullable(false)->change();
            $table->renameColumn('email_setorresponsavel', 'email_servicoposgraduacao');
            $table->dropForeign(['vinculo_id']);
            $table->dropColumn('vinculo_id');
        });
    }
}
