<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JoinVinculosAndParametrosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vinculos', function (Blueprint $table) {
            $table->boolean('exige_categoria')->nullable()->after('nome');
            $table->boolean('permite_programa')->nullable()->after('exige_categoria');
            $table->boolean('permite_taxa')->nullable()->after('permite_programa');
            $table->boolean('permite_nivel')->nullable()->after('permite_taxa');
            $table->boolean('permite_linhapesquisa')->nullable()->after('permite_nivel');
            $table->boolean('permite_disciplinas')->nullable()->after('permite_linhapesquisa');
            $table->boolean('exige_orientador')->nullable()->after('permite_disciplinas');
            $table->string('processos')->nullable()->after('exige_orientador');
            $table->string('boleto_codigo_fonte_recurso')->nullable()->after('processos');
            $table->string('boleto_estrutura_hierarquica')->nullable()->after('boleto_codigo_fonte_recurso');
            $table->string('boleto_momento_envio')->nullable()->after('boleto_estrutura_hierarquica');
            $table->string('link_inscricao_termos')->nullable()->after('boleto_momento_envio');
            $table->string('email_setorresponsavel')->nullable()->after('link_inscricao_termos');
            $table->string('email_secaoinformatica')->nullable()->after('email_setorresponsavel');
            $table->string('email_gerenciamentosite')->nullable()->after('email_secaoinformatica');
        });

        $parametros = DB::table('parametros')->get();
        foreach ($parametros as $parametro) {
            $processos = null;
            if ($parametro->permite_inscricao && $parametro->permite_matricula)
                $processos = 'Inscrição e Matrícula';
            elseif ($parametro->permite_inscricao)
                $processos = 'Inscrição';
            elseif ($parametro->permite_matricula)
                $processos = 'Matrícula';
            DB::table('vinculos')->where('id', $parametro->vinculo_id)->update([
                'exige_categoria' => $parametro->exige_categoria,
                'permite_programa' => $parametro->exige_programa,
                'permite_taxa' => $parametro->permite_taxa,
                'permite_nivel' => $parametro->permite_nivel,
                'permite_linhapesquisa' => $parametro->permite_linhapesquisa,
                'permite_disciplinas' => $parametro->permite_disciplinas,
                'exige_orientador' => $parametro->exige_orientador,
                'processos' => $processos,
                'boleto_codigo_fonte_recurso' => $parametro->boleto_codigo_fonte_recurso,
                'boleto_estrutura_hierarquica' => $parametro->boleto_estrutura_hierarquica,
                'boleto_momento_envio' => $parametro->boleto_momento_envio,
                'link_inscricao_termos' => $parametro->link_inscricao_termos,
                'email_setorresponsavel' => $parametro->email_setorresponsavel,
                'email_secaoinformatica' => $parametro->email_secaoinformatica,
                'email_gerenciamentosite' => $parametro->email_gerenciamentosite,
            ]);
        }

        Schema::table('vinculos', function (Blueprint $table) {
            $table->boolean('exige_categoria')->nullable(false)->change();
            $table->boolean('permite_programa')->nullable(false)->change();
            $table->boolean('permite_taxa')->nullable(false)->change();
            $table->boolean('permite_nivel')->nullable(false)->change();
            $table->boolean('permite_linhapesquisa')->nullable(false)->change();
            $table->boolean('permite_disciplinas')->nullable(false)->change();
            $table->boolean('exige_orientador')->nullable(false)->change();
        });

        Schema::dropIfExists('parametros');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vinculo_id');
            $table->boolean('exige_categoria');
            $table->boolean('exige_programa');
            $table->boolean('permite_inscricao');
            $table->boolean('permite_matricula');
            $table->boolean('permite_taxa');
            $table->boolean('exige_orientador');
            $table->boolean('permite_nivel');
            $table->boolean('permite_linhapesquisa');
            $table->boolean('permite_disciplinas');
            $table->string('boleto_codigo_fonte_recurso')->nullable();
            $table->string('boleto_estrutura_hierarquica')->nullable();
            $table->string('boleto_momento_envio')->nullable();
            $table->string('link_inscricao_termos')->nullable();
            $table->string('email_setorresponsavel')->nullable();
            $table->string('email_secaoinformatica')->nullable();
            $table->string('email_gerenciamentosite')->nullable();
            $table->timestamps();
        });

        $vinculos = DB::table('vinculos')->get();
        foreach ($vinculos as $vinculo) {
            $permite_inscricao = $vinculo->processos && str_contains($vinculo->processos, 'Inscrição') ? 1 : 0;
            $permite_matricula = $vinculo->processos && str_contains($vinculo->processos, 'Matrícula') ? 1 : 0;
            DB::table('parametros')->insert([
                'vinculo_id' => $vinculo->id,
                'exige_categoria' => $vinculo->exige_categoria,
                'exige_programa' => $vinculo->permite_programa,
                'permite_inscricao' => $permite_inscricao,
                'permite_matricula' => $permite_matricula,
                'permite_taxa' => $vinculo->permite_taxa,
                'exige_orientador' => $vinculo->exige_orientador,
                'permite_nivel' => $vinculo->permite_nivel,
                'permite_linhapesquisa' => $vinculo->permite_linhapesquisa,
                'permite_disciplinas' => $vinculo->permite_disciplinas,
                'boleto_codigo_fonte_recurso' => $vinculo->boleto_codigo_fonte_recurso,
                'boleto_estrutura_hierarquica' => $vinculo->boleto_estrutura_hierarquica,
                'boleto_momento_envio' => $vinculo->boleto_momento_envio,
                'link_inscricao_termos' => $vinculo->link_inscricao_termos,
                'email_setorresponsavel' => $vinculo->email_setorresponsavel,
                'email_secaoinformatica' => $vinculo->email_secaoinformatica,
                'email_gerenciamentosite' => $vinculo->email_gerenciamentosite,
                'created_at' => $vinculo->created_at,
                'updated_at' => $vinculo->updated_at,
            ]);
        }

        Schema::table('vinculos', function (Blueprint $table) {
            $table->dropColumn([
                'exige_categoria',
                'permite_programa',
                'permite_taxa',
                'permite_nivel',
                'permite_linhapesquisa',
                'permite_disciplinas',
                'exige_orientador',
                'processos',
                'boleto_codigo_fonte_recurso',
                'boleto_estrutura_hierarquica',
                'boleto_momento_envio',
                'link_inscricao_termos',
                'email_setorresponsavel',
                'email_secaoinformatica',
                'email_gerenciamentosite',
            ]);
        });
    }
}
