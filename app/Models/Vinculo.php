<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Vinculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'exige_categoria',
        'permite_programa',
        'permite_inscricao',
        'permite_matricula',
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
    ];

    // uso no crud generico
    protected const fields = [
        [
            'name' => 'nome',
            'label' => 'Nome',
        ],
        [
            'name' => 'modulos',
            'label' => 'Módulo(s) Utilizado(s)',
            'type' => 'checkbox_group',
            'data' => [
                'exige_categoria' => 'Categoria',
                'permite_programa' => 'Programa',
                'permite_taxa' => 'Taxa',
                'permite_nivel' => 'Nível',
                'permite_linhapesquisa' => 'Linha de Pesquisa/Tema',
                'permite_disciplinas' => 'Disciplinas',
                'exige_orientador' => 'Orientador',
            ],
        ],
        [
            'name' => 'processos',
            'label' => 'Processo(s) Utilizado(s)',
            'type' => 'select',
        ],
        [
            'name' => 'boleto_codigo_fonte_recurso',
            'label' => 'Código Fonte do Recurso para Boleto',
            'type' => 'integer',
        ],
        [
            'name' => 'boleto_estrutura_hierarquica',
            'label' => 'Estrutura Hierárquica para Boleto',
        ],
        [
            'name' => 'boleto_momento_envio',
            'label' => 'Momento de Geração e Envio do Boleto',
            'type' => 'radio',
            'data' => ['Envio da Inscrição/Matrícula' => 'Envio da Inscrição/Matrícula', 'Aprovação da Inscrição/Matrícula' => 'Aprovação da Inscrição/Matrícula'],
        ],
        [
            'name' => 'link_inscricao_termos',
            'label' => 'Link para os Termos de Inscrição',
        ],
        [
            'name' => 'email_setorresponsavel',
            'label' => 'E-mail do Setor Responsável',
        ],
        [
            'name' => 'email_secaoinformatica',
            'label' => 'E-mail da Seção de Informática',
        ],
        [
            'name' => 'email_gerenciamentosite',
            'label' => 'E-mail da Equipe de Gerenciamento do Site da Unidade',
        ],
    ];

    // uso no crud generico
    public static function getFields()
    {
        $processos = [
            'Inscrição' => 'Inscrição',
            'Inscrição e Matrícula' => 'Inscrição e Matrícula',
            'Matrícula' => 'Matrícula',
        ];

        $fields = self::fields;
        foreach ($fields as &$field) {
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect();
            } elseif ($field['name'] == 'processos')
                $field['data'] = $processos;

        }
        return $fields;
    }

    /**
     * retorna todos os programas
     * utilizado nas views common, para o select
     */
    public static function allToSelect()
    {
        $vinculos = self::get();
        $ret = [];
        foreach ($vinculos as $vinculo)
            if (Gate::allows('vinculos.view', $vinculo))
                $ret[$vinculo->id] = $vinculo->nome;
        return $ret;
    }

    public static function listarVinculos()
    {
        return self::get();
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
     * um vínculo se relaciona com n categorias
     */
    public function categorias()
    {
        return $this->hasMany('App\Models\Categoria');
    }

    /**
     * um vínculo se relaciona com n funções
     */
    public function funcoes()
    {
        return $this->belongsToMany('App\Models\Funcao', 'funcao_vinculo', 'vinculo_id', 'funcao_id')->withTimestamps();
    }

    /**
     * um vínculo se relaciona com n seleções
     */
    public function selecoes()
    {
        return $this->hasMany('App\Models\Selecao');
    }

    /**
     * um vínculo se relaciona com n tipos de arquivo
     */
    public function tiposarquivo()
    {
        return $this->belongsToMany('App\Models\TipoArquivo', 'tipoarquivo_vinculo', 'vinculo_id', 'tipoarquivo_id')->withTimestamps();
    }
}
