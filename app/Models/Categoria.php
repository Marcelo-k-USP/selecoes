<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'vinculo_id',
        'nome',
        'descricao',
        'exige_programa',
        'exige_nivel',
        'exige_linhapesquisa',
        'exige_disciplinas',
        'processos',
        'max_disciplinas',
        'link_acompanhamento',
    ];

    // uso no crud generico
    protected const fields = [
        [
            'name' => 'vinculo_id',
            'label' => 'Vínculo',
            'type' => 'select',
            'model' => 'Vinculo',
            'data' => [],
        ],
        [
            'name' => 'nome',
            'label' => 'Nome',
        ],
        [
            'name' => 'descricao',
            'label' => 'Descrição',
        ],
        [
            'name' => 'modulos',
            'label' => 'Módulo(s) Utilizado(s)',
            'type' => 'checkbox_group',
            'data' => [
                'exige_programa' => 'Programa',
                'exige_nivel' => 'Nível',
                'exige_linhapesquisa' => 'Linha de Pesquisa/Tema',
                'exige_disciplinas' => 'Disciplinas',
            ],
        ],
        [
            'name' => 'processos',
            'label' => 'Processo(s) Utilizado(s)',
            'type' => 'select',
        ],
        [
            'name' => 'max_disciplinas',
            'label' => 'Número Máximo de Disciplinas Permitidas ao Candidato',
            'type' => 'integer',
        ],
        [
            'name' => 'link_acompanhamento',
            'label' => 'Endereço no Site da Unidade para Acompanhamento do Processo pelos Candidatos',
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
        foreach ($fields as &$field)
            if (substr($field['name'], -3) == '_id') {
                if ($field['name'] == 'vinculo_id')
                    $field['data'] = Vinculo::where('exige_categoria', true)->pluck('nome', 'id')->toArray();
                else {
                    $class = '\\App\\Models\\' . $field['model'];
                    $field['data'] = $class::allToSelect();
                }
            } elseif ($field['name'] == 'processos')
                $field['data'] = $processos;

        return $fields;
    }

    /**
     * retorna todas as categorias
     * utilizado nas views common, para o select
     */
    public static function allToSelect()
    {
        $categorias = self::get();
        $ret = [];
        foreach ($categorias as $categoria)
            if (Gate::allows('categorias.view', $categoria))
                $ret[$categoria->id] = $categoria->nome;
        return $ret;
    }

    public static function listarCategorias()
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
     * uma categoria se relaciona com um vínculo
     */
    public function vinculo()
    {
        return $this->belongsTo('App\Models\Vinculo');
    }

    /**
     * uma categoria se relaciona com n seleções
     */
    public function selecoes()
    {
        return $this->hasMany('App\Models\Selecao');
    }

    /**
     * uma categoria se relaciona com n tipos de arquivo
     */
    public function tiposarquivo()
    {
        return $this->belongsToMany('App\Models\TipoArquivo', 'tipoarquivo_categoria', 'categoria_id', 'tipoarquivo_id')->withTimestamps();
    }
}
