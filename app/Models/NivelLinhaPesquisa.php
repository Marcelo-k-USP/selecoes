<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class NivelLinhaPesquisa extends Model
{
    use HasFactory;

    # nivel_linhapesquisa não segue convenção do laravel para nomes de tabela
    protected $table = 'nivel_linhapesquisa';

    public static function obterNiveisLinhasPesquisaPossiveis(?int $programa_id)
    {
        // todas as combinações de níveis em linhas de programa/temas possíveis para este usuário
        $programas = Auth::user()->listarProgramasGerenciados();
        $linhaspesquisa = LinhaPesquisa::whereIn('programa_id', $programas->pluck('id'));
        if (!is_null($programa_id))
            $linhaspesquisa->where('programa_id', $programa_id);
        return self::whereIn('linhapesquisa_id', $linhaspesquisa->get()->pluck('id'))->get();
    }

    public static function obterNiveisLinhasPesquisaDaSelecao(Selecao $selecao)
    {
        // todas as combinações de níveis em linhas de pesquisa nesta seleção
        $programas = Auth::user()->listarProgramasGerenciados();
        return $selecao->niveislinhaspesquisa->filter(function ($nivellinhapesquisa) use ($programas) {
            return $programas->pluck('id')->contains($nivellinhapesquisa->linhapesquisa->programa_id);
        });
    }

    /**
     * uma combinação nível com linha de pesquisa/tema se relaciona com n seleções
     */
    public function selecoes()
    {
        return $this->belongsToMany('App\Models\Selecao', 'selecao_nivellinhapesquisa', 'nivellinhapesquisa_id', 'selecao_id')->withTimestamps();
    }

    /**
     * uma combinação nível com linha de pesquisa se relaciona com um nível
     */
    public function nivel()
    {
        return $this->belongsTo('App\Models\Nivel', 'nivel_id');
    }

    /**
     * uma combinação nível com linha de pesquisa/tema se relaciona com uma linha de pesquisa/tema
     */
    public function linhapesquisa()
    {
        return $this->belongsTo('App\Models\LinhaPesquisa', 'linhapesquisa_id');
    }
}
