<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public const rules = [
        'vinculo_id' => ['required', 'integer'],
        'nome' => ['required', 'max:100'],
        'descricao' => ['max:255'],
        'exige_programa' => [],
        'exige_nivel' => [],
        'exige_linhapesquisa' => [],
        'exige_disciplinas' => [],
        'processos' => ['required', 'max:255'],
        'max_disciplinas' => ['nullable', 'integer'],
        'link_acompanhamento' => ['nullable', 'max:255', 'url', 'regex:/^(http:\/\/|https:\/\/)/'],
    ];

    public const messages =  [
        'vinculo_id.required' => 'O vínculo é obrigatório!',
        'vinculo_id.numeric' => 'O vínculo é inválido!',
        'nome.required' => 'O nome da categoria é obrigatório!',
        'nome.max' => 'O nome da categoria não pode exceder 100 caracteres!',
        'descricao.max' => 'A descrição da categoria não pode exceder 255 caracteres!',
        'processos.required' => 'O(s) processo(s) da categoria é(são) obrigatório(s)!',
        'processos.max' => 'O(s) processo(s) da categoria não pode(m) exceder 255 caracteres!',
        'max_disciplinas.integer' => 'O número máximo de disciplinas para o candidato é inválido!',
        'link_acompanhamento.max' => 'O endereço no site da unidade para acompanhamento do processo pelos candidatos não pode exceder 255 caracteres!',
        'link_acompanhamento.url' => 'O endereço no site da unidade para acompanhamento do processo pelos candidatos deve ser uma URL válida.',
        'link_acompanhamento.regex' => 'O endereço no site da unidade para acompanhamento do processo pelos candidatos deve começar com http:// ou https://',
    ];
}
