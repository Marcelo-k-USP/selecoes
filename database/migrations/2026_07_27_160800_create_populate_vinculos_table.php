<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopulateVinculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vinculos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->timestamps();
        });

        $agora = now();

        DB::table('vinculos')->insert([
            [
                'nome' => 'Pós-Graduação',
                'created_at' => $agora,
                'updated_at' => $agora
                ],
            [
                'nome' => 'Pós-Doc',
                'created_at' => $agora,
                'updated_at' => $agora
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vinculos');
    }
}
