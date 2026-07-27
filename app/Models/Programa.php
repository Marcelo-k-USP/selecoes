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
        $processos = [];
        $parametro = new Parametro();
        if ($parametro->permiteInscricao())
            $processos['Inscrição'] = 'Inscrição';
        if ($parametro->permiteInscricao() && $parametro->permiteMatricula())
            $processos['Inscrição e Matrícula'] = 'Inscrição e Matrícula';
        if ($parametro->permiteMatricula())
            $processos['Matrícula'] = 'Matrícula';

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
            if (Gate::allows('programas.view', $programa)) {
                $ret[$programa->id] = $programa->nomeCompleto();
            }
        return $ret;
    }

    public function obterPessoasFuncao(string $funcao_nome)
    {
        $funcao = Funcao::where('nome', $funcao_nome)->first();
        if (!$funcao)
            return collect();

        return $this->users()->wherePivot('funcao_id', $funcao->id)->select('users.id', 'users.name')->orderBy('users.name')->get();
    }

    public function obterResponsaveis()
    {
        $funcao_servico_posgraduacao = Funcao::where('nome', 'Serviço de Pós-Graduação')->first();
        $funcao_coordenadores_posgraduacao = Funcao::where('nome', 'Coordenadores(as) da Pós-Graduação')->first();

        return [
            [
                'funcao' => 'Docentes do Programa',
                'users' => $this->obterPessoasFuncao('Docentes do Programa'),
            ],
            [
                'funcao' => 'Secretários(as) do Programa',
                'users' => $this->obterPessoasFuncao('Secretários(as) do Programa'),
            ],
            [
                'funcao' => 'Coordenadores(as) do Programa',
                'users' => $this->obterPessoasFuncao('Coordenadores(as) do Programa'),
            ],
            [
                'funcao' => 'Serviço de Pós-Graduação',
                'users' => $funcao_servico_posgraduacao ? $funcao_servico_posgraduacao->users()->orderBy('users.name')->get() : collect(),
            ],
            [
                'funcao' => 'Coordenadores(as) da Pós-Graduação',
                'users' => $funcao_coordenadores_posgraduacao ? $funcao_coordenadores_posgraduacao->users()->orderBy('users.name')->get() : collect(),
            ],
        ];
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
     * Programa possui seleções
     */
    public function selecoes()
    {
        return $this->hasMany('App\Models\Selecao');
    }

    /**
     * Programa possui linhas de pesquisa/temas
     */
    public function linhaspesquisa()
    {
        return $this->hasMany('App\Models\LinhaPesquisa');
    }

    /**
     * relacionamento com níveis
     */
    public function niveis()
    {
        return $this->belongsToMany('App\Models\Nivel', 'nivel_programa', 'programa_id', 'nivel_id')->withTimestamps();
    }

    /**
     * relacionamento com users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_funcao', 'programa_id', 'user_id')->withPivot('funcao_id')->withTimestamps();
    }

    /**
     * relacionamento com funções
     */
    public function funcoes()
    {
        return $this->belongsToMany('App\Models\Funcao', 'user_funcao', 'programa_id', 'funcao_id')->withPivot('user_id')->withTimestamps();
    }

    public function nomeCompleto()
    {
        return $this->nome . ' (' . $this->sigla . ')';
    }
}
