<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VinculoRequest extends FormRequest
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
        'nome' => ['required', 'max:100'],
        'exige_categoria' => [],
        'permite_programa' => [],
        'permite_taxa' => [],
        'permite_nivel' => [],
        'permite_linhapesquisa' => [],
        'permite_disciplinas' => [],
        'exige_orientador' => [],
        'processos' => ['required', 'max:255'],
        'boleto_codigo_fonte_recurso' => ['required_with:permite_taxa', 'nullable', 'integer'],
        'boleto_estrutura_hierarquica' => ['required_with:permite_taxa', 'nullable', 'max:100'],
        'boleto_momento_envio' => ['required_with:permite_taxa', 'nullable', 'max:100'],
        'link_inscricao_termos' => ['required_if:processos,Inscrição,Inscrição e Matrícula', 'nullable', 'max:255', 'url', 'regex:/^(http:\/\/|https:\/\/)/'],
        'email_setorresponsavel' => ['nullable', 'max:255', 'email'],
        'email_secaoinformatica' => ['nullable', 'max:255', 'email'],
        'email_gerenciamentosite' => ['nullable', 'max:255', 'email'],
    ];

    public const messages = [
        'nome.required' => 'O nome do vínculo é obrigatório!',
        'nome.max' => 'O nome do vínculo não pode exceder 100 caracteres!',
        'processos.required' => 'O(s) processo(s) do vínculo é(são) obrigatório(s)!',
        'processos.max' => 'O(s) processo(s) do vínculo não pode(m) exceder 255 caracteres!',
        'boleto_codigo_fonte_recurso.required_with' => 'O código da fonte do recurso do boleto é obrigatório quando se permite taxa!',
        'boleto_estrutura_hierarquica.required_with' => 'A estrutura hierárquica do boleto é obrigatória quando se permite taxa!',
        'boleto_estrutura_hierarquica.max' => 'A estrutura hierárquica do boleto não pode exceder 100 caracteres!',
        'boleto_momento_envio.required_with' => 'O momento de geração e envio do boleto é obrigatório quando se permite taxa!',
        'boleto_momento_envio.max' => 'O momento de geração e envio do boleto não pode exceder 100 caracteres!',
        'link_inscricao_termos.required_if' => 'O link para os termos de inscrição é obrigatório quando se utiliza o processo de inscrição!',
        'link_inscricao_termos.max' => 'O link para os termos de inscrição não pode exceder 255 caracteres!',
        'link_inscricao_termos.url' => 'O link para os termos de inscrição deve ser uma URL válida.',
        'link_inscricao_termos.regex' => 'O link para os termos de inscrição deve começar com http:// ou https://',
        'email_setorresponsavel.max' => 'O e-mail do setor responsável não pode exceder 255 caracteres!',
        'email_setorresponsavel.email' => 'O e-mail do setor responsável deve ser válido.',
        'email_secaoinformatica.max' => 'O e-mail da seção de informática não pode exceder 255 caracteres!',
        'email_secaoinformatica.email' => 'O e-mail da seção de informática deve ser válido.',
        'email_gerenciamentosite.max' => 'O e-mail da equipe de gerenciamento do site não pode exceder 255 caracteres!',
        'email_gerenciamentosite.email' => 'O e-mail da equipe de gerenciamento do site deve ser válido.',
    ];
}
