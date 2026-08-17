<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameEstadoSolicitacoesIsencaoTaxa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mapping = [
            'Isenção de Taxa Solicitada' => 'Enviada',
            'Isenção de Taxa em Avaliação' => 'Em Avaliação',
            'Isenção de Taxa Aprovada' => 'Aprovada',
            'Isenção de Taxa Rejeitada' => 'Rejeitada',
            'Isenção de Taxa Aprovada Após Recurso' => 'Aprovada Após Recurso',
        ];
        foreach ($mapping as $old => $new)
            DB::table('solicitacoesisencaotaxa')->where('estado', $old)->update(['estado' => $new]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $mapping = [
            'Enviada' => 'Isenção de Taxa Solicitada',
            'Em Avaliação' => 'Isenção de Taxa em Avaliação',
            'Aprovada' => 'Isenção de Taxa Aprovada',
            'Rejeitada' => 'Isenção de Taxa Rejeitada',
            'Aprovada Após Recurso' => 'Isenção de Taxa Aprovada Após Recurso',
        ];
        foreach ($mapping as $new => $old)
            DB::table('solicitacoesisencaotaxa')->where('estado', $new)->update(['estado' => $old]);
    }
}
