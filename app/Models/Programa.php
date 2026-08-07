<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class Programa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
        'email_secretaria',
        'link_acompanhamento',
        'processos',
    ];

    // uso no crud generico
    protected const fields = [
        [
            'name' => 'nome',
            'label' => 'Nome',
        ],
        [
            'name' => 'sigla',
            'label' => 'Sigla',
        ],
        [
            'name' => 'descricao',
            'label' => 'Descrição',
        ],
        [
            'name' => 'email_secretaria',
            'label' => 'E-mail da Secretaria',
        ],
        [
            'name' => 'link_acompanhamento',
            'label' => 'Endereço no Site da Unidade para Acompanhamento do Processo pelos Candidatos',
        ],
        [
            'name' => 'processos',
            'label' => 'Processo(s) Utilizado(s)',
            'type' => 'select',
        ],
    ];

    // uso no crud generico
    public static function getFields()
    {
        // para o crud de programa, devo permitir todos os processos
        $processos = [
            'Inscrição' => 'Inscrição',
            'Inscrição e Matrícula' => 'Inscrição e Matrícula',
            'Matrícula' => 'Matrícula',
        ];

        $fields = self::fields;
        foreach ($fields as &$field)
            if ($field['name'] == 'processos') {
                $field['data'] = $processos;
                break;
            }
        return $fields;
    }

    /**
     * retorna todos os programas
     * utilizado nas views common, para o select
     */
    public static function allToSelect()
    {
        $programas = self::get();
        $ret = [];
        foreach ($programas as $programa)
            if (Gate::allows('programas.view', $programa))
                $ret[$programa->id] = $programa->nomeCompleto();
        return $ret;
    }

    /**
     * Menu Programas, lista os programas
     *
     * @return coleção de programas
     */
    public static function listarProgramas()
    {
        return self::orderBy('sigla')->get();
    }

    public function obterPessoasFuncao(string $funcao_nome)
    {
        $funcao = Funcao::where('nome', $funcao_nome)->first();
        if (!$funcao)
            return collect();

        return $this->users()->wherePivot('funcao_id', $funcao->id)->select('users.id', 'users.name', 'users.codpes', 'users.email')->orderBy('users.name')->get();
    }

    public function nomeCompleto()
    {
        return $this->nome . ' (' . $this->sigla . ')';
    }

    public function fazInscricoes()
    {
        return str_contains($this->processos, 'Inscrição');
    }

    public function fazMatriculas()
    {
        return str_contains($this->processos, 'Matrícula');
    }

    /**
     * um programa se relaciona com n seleções
     */
    public function selecoes()
    {
        return $this->hasMany('App\Models\Selecao');
    }

    /**
     * um programa se relaciona com n níveis
     */
    public function niveis()
    {
        return $this->belongsToMany('App\Models\Nivel', 'nivel_programa', 'programa_id', 'nivel_id')->withTimestamps();
    }

    /**
     * um programa se relaciona com n linhas de pesquisa/temas
     */
    public function linhaspesquisa()
    {
        return $this->hasMany('App\Models\LinhaPesquisa');
    }

    /**
     * um programa se relaciona com n funções
     */
    public function funcoes()
    {
        return $this->belongsToMany('App\Models\Funcao', 'user_funcao', 'programa_id', 'funcao_id')->withPivot('user_id')->withTimestamps();
    }

    /**
     * um programa se relaciona com n users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_funcao', 'programa_id', 'user_id')->withPivot('funcao_id')->withTimestamps();
    }
}
