<?php

namespace App\Models;

use App\Jobs\AlertaCandidatosIncompletude;
use App\Observers\SelecaoObserver;
use App\Utils\ClasseUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Uspdev\Replicado\Estrutura;

class Selecao extends Model
{
    use \Glorand\Model\Settings\Traits\HasSettingsField;
    use HasFactory;

    # selecoes não segue convenção do laravel para nomes de tabela
    protected $table = 'selecoes';

    public $defaultSettings = [
        'instrucoes' => '',
    ];

    public $settingsRules = [
        'instrucoes' => 'nullable',
    ];

    # valores default na criação de nova seleção
    # (os campos template_* de $attributes contêm modelos padrão para os formulários de solicitação de isenção de taxa, inscrição e matrícula da seleção)
    protected $attributes = [
        'estado' => 'Em Elaboração',
        'template_solicitacoesisencaotaxa' => '{
            "nome": {
                "label": "Nome",
                "type": "text",
                "validate": "required",
                "order": 0
            },
            "tipo_de_documento": {
                "label": "Tipo de Documento",
                "type": "select",
                "value": [
                {
                    "label": "RG",
                    "value": "rg",
                    "order": 0
                },
                {
                    "label": "RNE",
                    "value": "rne",
                    "order": 1
                },
                {
                    "label": "Passaporte",
                    "value": "passaporte",
                    "order": 2
                }
                ],
                "help": "Utilize o passaporte apenas se não possuir documento de identidade brasileira (RG)",
                "validate": "required",
                "order": 1
            },
            "numero_do_documento": {
                "label": "Número do Documento",
                "type": "text",
                "validate": "required",
                "order": 2
            },
            "cpf": {
                "label": "CPF",
                "type": "text",
                "validate": "required",
                "order": 3
            },
            "e_mail": {
                "label": "E-mail",
                "type": "email",
                "validate": "required",
                "order": 4
            }
        }',
        'template_inscricoes' => '{
            "nome": {
                "label": "Nome",
                "type": "text",
                "validate": "required",
                "order": 0
            },
            "nome_social": {
                "label": "Nome Social",
                "type": "text",
                "help": "Decreto Estadual n. 55.588, de 17/03/2010",
                "order": 1
            },
            "tipo_de_documento": {
                "label": "Tipo de Documento",
                "type": "select",
                "value": [
                    {
                        "label": "RG",
                        "value": "rg",
                        "order": 0
                    },
                    {
                        "label": "RNE",
                        "value": "rne",
                        "order": 1
                    },
                    {
                        "label": "Passaporte",
                        "value": "passaporte",
                        "order": 2
                    }
                ],
                "help": "Utilize o passaporte apenas se não possuir documento de identidade brasileira (RG)",
                "validate": "required",
                "order": 2
            },
            "numero_do_documento": {
                "label": "Número do Documento",
                "type": "text",
                "validate": "required",
                "order": 3
            },
            "data_vencto_passaporte": {
                "label": "Data de Vencimento do Passaporte",
                "type": "date",
                "order": 4
            },
            "cpf": {
                "label": "CPF",
                "type": "text",
                "validate": "required",
                "order": 5
            },
            "titulo_de_eleitor": {
                "label": "Título de Eleitor",
                "type": "text",
                "order": 6
            },
            "documento_militar": {
                "label": "Documento Militar",
                "type": "text",
                "help": "Quando pertinente",
                "order": 7
            },
            "nome_da_mae": {
                "label": "Nome da Mãe",
                "type": "text",
                "validate": "required",
                "order": 8
            },
            "nome_do_pai": {
                "label": "Nome do Pai",
                "type": "text",
                "order": 9
            },
            "data_de_nascimento": {
                "label": "Data de Nascimento",
                "type": "date",
                "validate": "required",
                "order": 10
            },
            "local_de_nascimento": {
                "label": "Local de Nascimento",
                "type": "text",
                "validate": "required",
                "order": 11
            },
            "uf_de_nascimento": {
                "label": "UF de Nascimento",
                "type": "select",
                "value": [
                    {
                        "label": "AC",
                        "value": "ac",
                        "order": 0
                    },
                    {
                        "label": "AL",
                        "value": "al",
                        "order": 1
                    },
                    {
                        "label": "AM",
                        "value": "am",
                        "order": 2
                    },
                    {
                        "label": "AP",
                        "value": "ap",
                        "order": 3
                    },
                    {
                        "label": "BA",
                        "value": "ba",
                        "order": 4
                    },
                    {
                        "label": "CE",
                        "value": "ce",
                        "order": 5
                    },
                    {
                        "label": "DF",
                        "value": "df",
                        "order": 6
                    },
                    {
                        "label": "ES",
                        "value": "es",
                        "order": 7
                    },
                    {
                        "label": "GO",
                        "value": "go",
                        "order": 8
                    },
                    {
                        "label": "MA",
                        "value": "ma",
                        "order": 9
                    },
                    {
                        "label": "MG",
                        "value": "mg",
                        "order": 10
                    },
                    {
                        "label": "MS",
                        "value": "ms",
                        "order": 11
                    },
                    {
                        "label": "MT",
                        "value": "mt",
                        "order": 12
                    },
                    {
                        "label": "PA",
                        "value": "pa",
                        "order": 13
                    },
                    {
                        "label": "PB",
                        "value": "pb",
                        "order": 14
                    },
                    {
                        "label": "PE",
                        "value": "pe",
                        "order": 15
                    },
                    {
                        "label": "PI",
                        "value": "pi",
                        "order": 16
                    },
                    {
                        "label": "PR",
                        "value": "pr",
                        "order": 17
                    },
                    {
                        "label": "RJ",
                        "value": "rj",
                        "order": 18
                    },
                    {
                        "label": "RN",
                        "value": "rn",
                        "order": 19
                    },
                    {
                        "label": "RO",
                        "value": "ro",
                        "order": 20
                    },
                    {
                        "label": "RR",
                        "value": "rr",
                        "order": 21
                    },
                    {
                        "label": "RS",
                        "value": "rs",
                        "order": 22
                    },
                    {
                        "label": "SC",
                        "value": "sc",
                        "order": 23
                    },
                    {
                        "label": "SE",
                        "value": "se",
                        "order": 24
                    },
                    {
                        "label": "SP",
                        "value": "sp",
                        "order": 25
                    },
                    {
                        "label": "TO",
                        "value": "to",
                        "order": 26
                    }
                ],
                "validate": "required",
                "order": 12
            },
            "sexo": {
                "label": "Sexo",
                "type": "select",
                "value": [
                    {
                        "label": "Masculino",
                        "value": "masculino",
                        "order": 0
                    },
                    {
                        "label": "Feminino",
                        "value": "feminino",
                        "order": 1
                    }
                ],
                "validate": "required",
                "order": 13
            },
            "raca_cor": {
                "label": "Raça/Cor",
                "type": "select",
                "value": [
                    {
                        "label": "Amarela",
                        "value": "amarela",
                        "order": 0
                    },
                    {
                        "label": "Branca",
                        "value": "branca",
                        "order": 1
                    },
                    {
                        "label": "Indígena",
                        "value": "indigena",
                        "order": 2
                    },
                    {
                        "label": "Parda",
                        "value": "parda",
                        "order": 3
                    },
                    {
                        "label": "Preta",
                        "value": "preta",
                        "order": 4
                    },
                    {
                        "label": "Prefiro Não Responder",
                        "value": "prefiro_nao_responder",
                        "order": 5
                    }
                ],
                "validate": "required",
                "order": 14
            },
            "declaro_ppi": {
                "label": "Declaro, para os devidos fins, que sou preto, pardo ou indígena",
                "type": "radio",
                "value": [
                    {
                        "label": "Não",
                        "value": "nao",
                        "order": 0
                    },
                    {
                        "label": "Sim",
                        "value": "sim",
                        "order": 1
                    }
                ],
                "validate": "required",
                "order": 15
            },
            "portador_de_deficiencia": {
                "label": "Portador de Deficiência",
                "type": "radio",
                "value": [
                    {
                        "label": "Não",
                        "value": "nao",
                        "order": 0
                    },
                    {
                        "label": "Sim",
                        "value": "sim",
                        "order": 1
                    }
                ],
                "validate": "required",
                "order": 16
            },
            "qual_a_sua_deficiencia": {
                "label": "Qual a sua deficiência",
                "type": "text",
                "order": 17
            },
            "condicoes_prova": {
                "label": "Condições Necessárias para a Realização da Prova",
                "type": "textarea",
                "order": 18
            },
            "cep": {
                "label": "CEP",
                "type": "text",
                "validate": "required",
                "order": 19
            },
            "endereco_residencial": {
                "label": "Endereço Residencial",
                "type": "text",
                "validate": "required",
                "order": 20
            },
            "numero": {
                "label": "Número",
                "type": "text",
                "validate": "required",
                "order": 21
            },
            "complemento": {
                "label": "Complemento",
                "type": "text",
                "order": 22
            },
            "bairro": {
                "label": "Bairro",
                "type": "text",
                "validate": "required",
                "order": 23
            },
            "cidade": {
                "label": "Cidade",
                "type": "text",
                "validate": "required",
                "order": 24
            },
            "uf": {
                "label": "UF",
                "type": "select",
                "value": [
                    {
                        "label": "AC",
                        "value": "ac",
                        "order": 0
                    },
                    {
                        "label": "AL",
                        "value": "al",
                        "order": 1
                    },
                    {
                        "label": "AM",
                        "value": "am",
                        "order": 2
                    },
                    {
                        "label": "AP",
                        "value": "ap",
                        "order": 3
                    },
                    {
                        "label": "BA",
                        "value": "ba",
                        "order": 4
                    },
                    {
                        "label": "CE",
                        "value": "ce",
                        "order": 5
                    },
                    {
                        "label": "DF",
                        "value": "df",
                        "order": 6
                    },
                    {
                        "label": "ES",
                        "value": "es",
                        "order": 7
                    },
                    {
                        "label": "GO",
                        "value": "go",
                        "order": 8
                    },
                    {
                        "label": "MA",
                        "value": "ma",
                        "order": 9
                    },
                    {
                        "label": "MG",
                        "value": "mg",
                        "order": 10
                    },
                    {
                        "label": "MS",
                        "value": "ms",
                        "order": 11
                    },
                    {
                        "label": "MT",
                        "value": "mt",
                        "order": 12
                    },
                    {
                        "label": "PA",
                        "value": "pa",
                        "order": 13
                    },
                    {
                        "label": "PB",
                        "value": "pb",
                        "order": 14
                    },
                    {
                        "label": "PE",
                        "value": "pe",
                        "order": 15
                    },
                    {
                        "label": "PI",
                        "value": "pi",
                        "order": 16
                    },
                    {
                        "label": "PR",
                        "value": "pr",
                        "order": 17
                    },
                    {
                        "label": "RJ",
                        "value": "rj",
                        "order": 18
                    },
                    {
                        "label": "RN",
                        "value": "rn",
                        "order": 19
                    },
                    {
                        "label": "RO",
                        "value": "ro",
                        "order": 20
                    },
                    {
                        "label": "RR",
                        "value": "rr",
                        "order": 21
                    },
                    {
                        "label": "RS",
                        "value": "rs",
                        "order": 22
                    },
                    {
                        "label": "SC",
                        "value": "sc",
                        "order": 23
                    },
                    {
                        "label": "SE",
                        "value": "se",
                        "order": 24
                    },
                    {
                        "label": "SP",
                        "value": "sp",
                        "order": 25
                    },
                    {
                        "label": "TO",
                        "value": "to",
                        "order": 26
                    }
                ],
                "validate": "required",
                "order": 25
            },
            "celular": {
                "label": "Celular",
                "type": "text",
                "validate": "required",
                "order": 26
            },
            "e_mail": {
                "label": "E-mail",
                "type": "email",
                "validate": "required",
                "order": 27
            },
            "declaro_concordo_termos": {
                "label": "Declaro estar ciente e concordo com os <a href=\"{{UNIDADE_LINK_INSCRICAO_TERMOS}}\">termos de inscrição no Programa de Pós-Graduação d{{UNIDADE_GENERO}} {{UNIDADE_NOME}}</a>",
                "type": "checkbox",
                "validate": "required",
                "order": 28
            },
            "declaro_revisei_inscricao": {
                "label": "Declaro que revisei todas as informações inseridas neste formulário e que elas estão corretas, e venho requerer minha inscrição como candidato(a) à vaga no Programa de Pós-Graduação n{{UNIDADE_GENERO}} {{UNIDADE_NOME}}",
                "type": "checkbox",
                "validate": "required",
                "order": 29
            },
            "declaro_ciente_nao_presencial": {
                "label": "Declaro estar ciente de que o processo seletivo será realizado no formato não presencial, on-line, e que a <u>Comissão de Seleção não se responsabiliza por eventuais falhas técnicas por parte do(a) candidato(a) (tais como falta de internet, cortes de som, corte de luz, etc.) durante a realização das provas e das arguições relizadas online</u>. A sugestão é que o(a) candidato(a) se organize com antecedência para o bom andamento da prova",
                "type": "checkbox",
                "validate": "required",
                "order": 30
            }
        }',
        'template_matriculas' => '{
            "nome": {
                "label": "Nome",
                "type": "text",
                "validate": "required",
                "order": 0
            },
            "tipo_de_documento": {
                "label": "Tipo de Documento",
                "type": "select",
                "value": [
                {
                    "label": "RG",
                    "value": "rg",
                    "order": 0
                },
                {
                    "label": "RNE",
                    "value": "rne",
                    "order": 1
                },
                {
                    "label": "Passaporte",
                    "value": "passaporte",
                    "order": 2
                }
                ],
                "help": "Utilize o passaporte apenas se não possuir documento de identidade brasileira (RG)",
                "validate": "required",
                "order": 1
            },
            "numero_do_documento": {
                "label": "Número do Documento",
                "type": "text",
                "validate": "required",
                "order": 2
            },
            "cpf": {
                "label": "CPF",
                "type": "text",
                "validate": "required",
                "order": 3
            },
            "e_mail": {
                "label": "E-mail",
                "type": "email",
                "validate": "required",
                "order": 4
            }
        }',
    ];

    protected $fillable = [
        'ingresso_semestre',
        'ingresso_ano',
        'nome',
        'descricao',
        'estado',
        'vinculo_id',
        'categoria_id',
        'programa_id',
        'tem_taxa',
        'fluxo_continuo',
        'solicitacoesisencaotaxa_datahora_inicio',
        'solicitacoesisencaotaxa_datahora_fim',
        'inscricoes_datahora_inicio',
        'inscricoes_datahora_fim',
        'matriculas_datahora_inicio',
        'matriculas_datahora_fim',
        'boleto_data_vencimento',
        'boleto_offset_vencimento',
        'boleto_valor',
        'boleto_texto',
        'email_inscricaoaprovacao_texto',
        'email_inscricaorejeicao_texto',
        'email_matriculaaprovacao_texto',
        'email_matricularejeicao_texto',
        'template_solicitacoesisencaotaxa',
        'template_inscricoes',
        'template_matriculas',
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
            'name' => 'categoria_id',
            'label' => 'Categoria',
            'type' => 'select',
            'model' => 'Categoria',
            'data' => [],
        ],
        [
            'name' => 'programa_id',
            'label' => 'Programa',
            'type' => 'select',
            'model' => 'Programa',
            'data' => [],
        ],
        [
            'name' => 'ingresso_semestre',
            'label' => 'Semestre de Ingresso',
            'type' => 'radio',
            'data' => ['0' => 'Não definido', '1' => '1º', '2' => '2º'],
        ],
        [
            'name' => 'ingresso_ano',
            'label' => 'Ano de Ingresso',
            'type' => 'select',
            'data' => [],
            ],
        [
            'name' => 'descricao',
            'label' => 'Descrição',
        ],
        [
            'name' => 'tem_taxa',
            'label' => 'Taxa de Inscrição para a Seleção',    // a view altera este label dinamicamente
            'type' => 'checkbox',
        ],
        [
            'name' => 'fluxo_continuo',
            'label' => 'Fluxo Contínuo (os fluxos ocorrem no mesmo período)',
            'type' => 'checkbox',
        ],
        [
            'name' => 'solicitacoesisencaotaxa_datahora_inicio',
            'label' => 'Início das Solicitações de Isenção de Taxa',
            'type' => 'datetime',
        ],
        [
            'name' => 'solicitacoesisencaotaxa_datahora_fim',
            'label' => 'Fim das Solicitações de Isenção de Taxa',
            'type' => 'datetime',
        ],
        [
            'name' => 'inscricoes_datahora_inicio',
            'label' => 'Início das Inscrições',
            'type' => 'datetime',
        ],
        [
            'name' => 'inscricoes_datahora_fim',
            'label' => 'Fim das Inscrições',
            'type' => 'datetime',
        ],
        [
            'name' => 'matriculas_datahora_inicio',
            'label' => 'Início das Matrículas',
            'type' => 'datetime',
        ],
        [
            'name' => 'matriculas_datahora_fim',
            'label' => 'Fim das Matrículas',
            'type' => 'datetime',
        ],
        [
            'name' => 'boleto_data_vencimento',
            'label' => 'Data de Vencimento do Boleto',
            'type' => 'date',
        ],
        [
            'name' => 'boleto_offset_vencimento',
            'label' => 'Dias Úteis para Pagamento do Boleto',
            'type' => 'integer',
        ],
        [
            'name' => 'boleto_valor',
            'label' => 'Valor do Boleto (R$)',
            'type' => 'number',
        ],
        [
            'name' => 'boleto_texto',
            'label' => 'Eventuais Informações Adicionais no Boleto',
        ],
        [
            'name' => 'email_inscricaoaprovacao_texto',
            'label' => 'Eventuais Informações Adicionais no E-mail de Aprovação da Inscrição',
        ],
        [
            'name' => 'email_inscricaorejeicao_texto',
            'label' => 'Eventuais Informações Adicionais no E-mail de Rejeição da Inscrição',
        ],
        [
            'name' => 'email_matriculaaprovacao_texto',
            'label' => 'Eventuais Informações Adicionais no E-mail de Aprovação da Matrícula',
        ],
        [
            'name' => 'email_matricularejeicao_texto',
            'label' => 'Eventuais Informações Adicionais no E-mail de Rejeição da Matrícula',
        ],
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        Selecao::observe(SelecaoObserver::class);
    }

    public function injetarUnidadeNosTemplates()
    {
        $this->injetarUnidadeNoTemplate('SolicitacaoIsencaoTaxa');
        $this->injetarUnidadeNoTemplate('Inscricao');
        $this->injetarUnidadeNoTemplate('Matricula');
    }

    private function injetarUnidadeNoTemplate(string $classe_nome)
    {
        $template_nome = 'template_' . ClasseUtils::obterClasseNomePlural($classe_nome);

        if (empty($this->attributes[$template_nome]))
            return;

        // se não houver nenhuma tag para substituir, aborta aqui mesmo... isso previne repetição inútil caso o model seja "tocado" várias vezes
        if (!str_contains($this->attributes[$template_nome], '{{UNIDADE_'))
            return;

        $this->attributes[$template_nome] = str_replace(
            '{{UNIDADE_LINK_INSCRICAO_TERMOS}}',
            $this->vinculo?->link_inscricao_termos ?? '#',
            $this->attributes[$template_nome]
        );

        $this->attributes[$template_nome] = str_replace(
            '{{UNIDADE_GENERO}}',
            Estrutura::obterUnidade(config('senhaunica.codigoUnidade'))['artttm'] ?? 'o(a)',
            $this->attributes[$template_nome]
        );

        $this->attributes[$template_nome] = str_replace(
            '{{UNIDADE_NOME}}',
            Estrutura::obterUnidade(config('senhaunica.codigoUnidade'))['nomund'] ?? 'Unidade',
            $this->attributes[$template_nome]
        );
    }

    // uso no crud generico
    public static function getFields()
    {
        $fields = self::fields;
        foreach ($fields as &$field)
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect();
            } elseif ($field['name'] == 'ingresso_ano') {
                $anoAtual = date('Y');
                $field['data'] = array_combine(range($anoAtual, $anoAtual + 3), range($anoAtual, $anoAtual + 3));
            }
        return $fields;
    }

    /**
     * template
     * retorna os campos dos templates dos formulários
     */
    public static function getTemplateFields()
    {
        return ['label', 'type', 'can', 'visible_campo', 'visible_valor', 'help', 'value', 'validate'];
    }

    /**
     * retorna todas as seleções autorizadas para o usuário
     * utilizado nas views common, para o select
     */
    public static function allToSelect()
    {
        $selecoes = self::get();
        $ret = [];
        foreach ($selecoes as $selecao)
            if (Gate::allows('selecoes.view'))
                $ret[$selecao->id] = $selecao->nome . ($selecao->exigeCategoria() ? ' (' . $selecao->categoria->nome . ')' : '');
        return $ret;
    }

    /**
     * lista de estados padrão
     */
    public static function estados()
    {
        return ['Em Elaboração',
                'Aguardando Início das Solicitações de Isenção de Taxa e das Inscrições'                , 'Período de Solicitações de Isenção de Taxa e de Inscrições',                   // usados nos casos de fluxo contínuo com taxa
                'Aguardando Início das Solicitações de Isenção de Taxa, das Inscrições e das Matrículas', 'Período de Solicitações de Isenção de Taxa, de Inscrições e de Matrículas',    // usados nos casos de fluxo contínuo com taxa
                'Aguardando Início das Solicitações de Isenção de Taxa e das Matrículas'                , 'Período de Solicitações de Isenção de Taxa e de Matrículas',                   // usados nos casos de fluxo contínuo com taxa
                'Aguardando Início das Solicitações de Isenção de Taxa'                                 , 'Período de Solicitações de Isenção de Taxa',                                   // usados nos casos de fluxo normal com taxa
                'Aguardando Início das Inscrições', 'Periodo de Inscrições',
                'Aguardando Início das Matrículas', 'Periodo de Matrículas',
                'Encerrada'];
    }

    /**
     * Menu Seleções, lista as seleções
     *
     * @return coleção de seleções
     */
    public static function listarSelecoes()
    {
        if (session('perfil') === 'admin')
            return self::all();

        $vinculosIds = Auth::user()->funcoes()->with('vinculos')->get()->pluck('vinculos.*.id')->flatten()->unique();
        return self::whereIn('vinculo_id', $vinculosIds)->where(function ($query) {
            $query->whereIn('programa_id', Auth::user()->listarProgramasGerenciados()->pluck('id'))               // seleções de programas que o usuário gerencia, e também...
                    ->orWhere(function ($query) {
                        $query->where('categoria_id', Categoria::where('nome', 'Aluno Especial')->value('id'))    // seleções para alunos especiais (sem programa), desde que, neste segundo caso...
                            ->where(function ($query) {
                            $query->whereExists(function ($query) {
                                $query->selectRaw(1)
                                    ->from('user_funcao')
                                    ->join('funcoes', 'funcoes.id', '=', 'user_funcao.funcao_id')
                                    ->where('user_funcao.user_id', Auth::id())
                                    ->whereIn('funcoes.grupo', ['Funcionários(as) do Setor', 'Coordenadores(as) do Setor']);    // ou que o usuário seja do grupo Funcionários(as) do Setor ou Coordenadores(as) do Setor
                            });
                        });
                    });
                })
                ->get();
    }

    /**
     * Mostra lista de vínculos e respectivas seleções
     * para selecionar e solicitar isenção de taxa
     *
     * @return \Illuminate\Http\Response
     */
    public static function listarSelecoesParaNovaSolicitacaoIsencaoTaxa()
    {
        // obtém os vínculos com suas categorias e respectivas seleções, bem como com suas seleções
        $vinculos = Vinculo::with([ 'categorias.selecoes.niveislinhaspesquisa.nivel', 'selecoes.niveislinhaspesquisa.nivel' ])->get();

        // filtro para aceitar somente as seleções que estejam em período de solicitações de isenção de taxa
        $filtro = fn($selecao) =>
            (str_starts_with($selecao->estado, 'Período de Solicitações de Isenção de Taxa'));

        // tratamento dos níveis com linhas de pesquisa/temas
        $tratamento_niveis = function ($selecoes) {
            foreach ($selecoes as $selecao)
                $selecao->niveis = $selecao->niveislinhaspesquisa->sortBy('nivel_id')->pluck('nivel')->unique();
            return $selecoes;
        };

        foreach ($vinculos as $vinculo)
            if ($vinculo->categorias->isEmpty())
                $vinculo->setRelation('selecoes', $tratamento_niveis($vinculo->selecoes->filter($filtro)));    // coloca as seleções direto no vínculo
            else {
                $vinculo->setRelation('selecoes', collect());    // coloca as seleções dentro de cada categoria
                foreach ($vinculo->categorias as $categoria)
                    $categoria->setRelation('selecoes', $tratamento_niveis($categoria->selecoes->filter($filtro)));
            }

        // elimina vínculos sem seleções
        $vinculos = $vinculos->filter(function ($vinculo) {
            if ($vinculo->categorias->isEmpty())
                return $vinculo->selecoes->isNotEmpty();
            else {
                $categoriasComSelecao = $vinculo->categorias->filter(fn($categoria) => $categoria->selecoes->isNotEmpty());
                $vinculo->setRelation('categorias', $categoriasComSelecao);
                return $categoriasComSelecao->isNotEmpty();
            }
        });

        return $vinculos;
    }

    /**
     * Mostra lista de vínculos e respectivas seleções
     * para selecionar e criar nova inscrição
     *
     * @return \Illuminate\Http\Response
     */
    public static function listarSelecoesParaNovaInscricao()
    {
        // obtém os vínculos com suas categorias e respectivas seleções, bem como com suas seleções
        $vinculos = Vinculo::with([ 'categorias.selecoes.niveislinhaspesquisa.nivel', 'selecoes.niveislinhaspesquisa.nivel' ])->get();

        // filtro para aceitar somente as seleções que estejam em período de inscrições
        $filtro = fn($selecao) =>
            (str_starts_with($selecao->estado, 'Período de') && str_contains($selecao->estado, 'Inscrições'))
            && $selecao->fazInscricoes();

        // tratamento dos níveis com linhas de pesquisa/temas
        $tratamento_niveis = function ($selecoes) {
            foreach ($selecoes as $selecao)
                $selecao->niveis = $selecao->niveislinhaspesquisa->sortBy('nivel_id')->pluck('nivel')->unique();
            return $selecoes;
        };

        foreach ($vinculos as $vinculo)
            if ($vinculo->categorias->isEmpty())
                $vinculo->setRelation('selecoes', $tratamento_niveis($vinculo->selecoes->filter($filtro)));    // coloca as seleções direto no vínculo
            else {
                $vinculo->setRelation('selecoes', collect());    // coloca as seleções dentro de cada categoria
                foreach ($vinculo->categorias as $categoria)
                    $categoria->setRelation('selecoes', $tratamento_niveis($categoria->selecoes->filter($filtro)));
            }

        // elimina vínculos sem seleções
        $vinculos = $vinculos->filter(function ($vinculo) {
            if ($vinculo->categorias->isEmpty())
                return $vinculo->selecoes->isNotEmpty();
            else {
                $categoriasComSelecao = $vinculo->categorias->filter(fn($categoria) => $categoria->selecoes->isNotEmpty());
                $vinculo->setRelation('categorias', $categoriasComSelecao);
                return $categoriasComSelecao->isNotEmpty();
            }
        });

        return $vinculos;
    }

    /**
     * Mostra lista de vínculos e respectivas seleções
     * para selecionar e criar nova matrícula
     *
     * @return \Illuminate\Http\Response
     */
    public static function listarSelecoesParaNovaMatricula()
    {
        // obtém os vínculos com suas categorias e respectivas seleções, bem como com suas seleções
        $vinculos = Vinculo::with([ 'categorias.selecoes.niveislinhaspesquisa.nivel', 'selecoes.niveislinhaspesquisa.nivel' ])->get();

        // filtro para aceitar somente as seleções que estejam em período de matrículas
        $filtro = fn($selecao) =>
            (str_starts_with($selecao->estado, 'Período de') && str_contains($selecao->estado, 'Matrículas'))
            && $selecao->fazMatriculas();

        // tratamento dos níveis com linhas de pesquisa/temas
        $tratamento_niveis = function ($selecoes) {
            foreach ($selecoes as $selecao)
                $selecao->niveis = $selecao->niveislinhaspesquisa->sortBy('nivel_id')->pluck('nivel')->unique();
            return $selecoes;
        };

        foreach ($vinculos as $vinculo)
            if ($vinculo->categorias->isEmpty())
                $vinculo->setRelation('selecoes', $tratamento_niveis($vinculo->selecoes->filter($filtro)));    // coloca as seleções direto no vínculo
            else {
                $vinculo->setRelation('selecoes', collect());    // coloca as seleções dentro de cada categoria
                foreach ($vinculo->categorias as $categoria)
                    $categoria->setRelation('selecoes', $tratamento_niveis($categoria->selecoes->filter($filtro)));
            }

        // elimina vínculos sem seleções
        $vinculos = $vinculos->filter(function ($vinculo) {
            if ($vinculo->categorias->isEmpty())
                return $vinculo->selecoes->isNotEmpty();
            else {
                $categoriasComSelecao = $vinculo->categorias->filter(fn($categoria) => $categoria->selecoes->isNotEmpty());
                $vinculo->setRelation('categorias', $categoriasComSelecao);
                return $categoriasComSelecao->isNotEmpty();
            }
        });

        return $vinculos;
    }

    /**
     * Atualiza o status da seleção
     * Esta é a máquina de fluxo de estados da seleção
     */
    public function atualizarStatus()
    {
        $tiposarquivo_required = TipoArquivo::where('classe_nome', 'Seleções')
            ->whereHas('vinculos', function ($query) {
                $query->where('vinculos.id', $this->vinculo_id);
            })->where('obrigatorio', true)->pluck('nome')->filter(function ($nome) {
                return ($nome !== 'Normas para Isenção de Taxa') || $this->tem_taxa;
            })->toArray();
        $possui_todos_os_arquivos_required = true;
        foreach ($tiposarquivo_required as $tipoarquivo_required)
            if (!$this->arquivos->contains('pivot.tipo', $tipoarquivo_required)) {
                $possui_todos_os_arquivos_required = false;
                break;
            }

        $outras_condicoes_satisfeitas = true;
        if ($this->exigeNivel() && $this->exigeLinhaPesquisa())
            $outras_condicoes_satisfeitas &= !$this->niveislinhaspesquisa->isEmpty();
        if ($this->exigeDisciplinas())
            $outras_condicoes_satisfeitas &= !$this->disciplinas->isEmpty();

        if (!$possui_todos_os_arquivos_required || !$outras_condicoes_satisfeitas)
            $this->update(['estado' => 'Em Elaboração']);
        else {
            $agora = Carbon::now();

            $estados_abreviados = [];
            if ($this->fluxo_continuo) {
                // neste caso, os três fluxos ocorrem concomitantemente, então tanto faz pegar as datas das solicitações de isenção de taxa, das inscrições ou das matrículas
                if ($this->fazInscricoes() && $this->fazMatriculas())
                    $estados_abreviados[] = ['nome_das' => 'das Solicitações de Isenção de Taxa, das Inscrições e das Matrículas', 'nome_de' => 'de Solicitações de Isenção de Taxa, de Inscrições e de Matrículas', 'inicio' => $this->inscricoes_datahora_inicio, 'fim' => $this->inscricoes_datahora_fim];
                elseif ($this->fazInscricoes())
                    $estados_abreviados[] = ['nome_das' => 'das Solicitações de Isenção de Taxa e das Inscrições', 'nome_de' => 'de Solicitações de Isenção de Taxa e de Inscrições', 'inicio' => $this->inscricoes_datahora_inicio, 'fim' => $this->inscricoes_datahora_fim];
                elseif ($this->fazMatriculas())
                    $estados_abreviados[] = ['nome_das' => 'das Solicitações de Isenção de Taxa e das Matrículas', 'nome_de' => 'de Solicitações de Isenção de Taxa e de Matrículas', 'inicio' => $this->matriculas_datahora_inicio, 'fim' => $this->matriculas_datahora_fim];
            } else {
                if ($this->tem_taxa)
                    $estados_abreviados[] = ['nome_das' => 'das Solicitações de Isenção de Taxa', 'nome_de' => 'de Solicitações de Isenção de Taxa', 'inicio' => $this->solicitacoesisencaotaxa_datahora_inicio, 'fim' => $this->solicitacoesisencaotaxa_datahora_fim];
                if ($this->fazInscricoes())
                    $estados_abreviados[] = ['nome_das' => 'das Inscrições', 'nome_de' => 'de Inscrições', 'inicio' => $this->inscricoes_datahora_inicio, 'fim' => $this->inscricoes_datahora_fim];
                if ($this->fazMatriculas())
                    $estados_abreviados[] = ['nome_das' => 'das Matrículas', 'nome_de' => 'de Matrículas', 'inicio' => $this->matriculas_datahora_inicio, 'fim' => $this->matriculas_datahora_fim];
            }

            $estado_calculado = 'Encerrada';
            foreach ($estados_abreviados as $estado_abreviado)
                if ($agora < $estado_abreviado['inicio']) {
                    $estado_calculado = 'Aguardando Início ' . $estado_abreviado['nome_das'];
                    break;
                } elseif ($agora <= $estado_abreviado['fim']) {
                    $estado_calculado = 'Período ' . $estado_abreviado['nome_de'];
                    break;
                }
            $this->update(['estado' => $estado_calculado]);
        }
    }

    public function reagendarTarefas()
    {
        // este método é invocado tanto na criação quanto na alteração de seleção
        // quando o usuário altera uma seleção, eventualmente ele pode alterar as datas de fim
        // neste caso, ao invés de alterarmos as datas/horas dos jobs da seleção, simplesmente os removemos e os recriamos logo em seguida, considerando as datas de fim eventualmente alteradas

        // remove jobs de alerta de solicitações de isenção de taxa, inscrições e matrículas não concluídas
        foreach (DB::table('jobs')->where('payload->displayName', 'App\Jobs\AlertaCandidatosIncompletude')->get() as $job) {
            $command = obterCommandDoJob($job);
            if ($command) {
                $selecao_id = obterPropriedadePrivada($command, 'selecao_id');
                if ($selecao_id == $this->id)
                    DB::table('jobs')->where('id', $job->id)->delete();
            }
        }

        // (re)agenda job de alerta de solicitações de isenção de taxa não concluídas
        if ($this->tem_taxa) {
            $job_datahora = Carbon::parse($this->solicitacoesisencaotaxa_datahora_fim)->subHours(24);
            if ($job_datahora > now())
                AlertaCandidatosIncompletude::dispatch($this->id, 'SolicitacaoIsencaoTaxa')->delay($job_datahora);
        }

        if ($this->fazInscricoes()) {
            // (re)agenda job de alerta de inscrições não concluídas
            $job_datahora = Carbon::parse($this->inscricoes_datahora_fim)->subHours(24);
            if ($job_datahora > now())
                AlertaCandidatosIncompletude::dispatch($this->id, 'Inscricao')->delay($job_datahora);
        }

        if ($this->fazMatriculas()) {
            // (re)agenda job de alerta de matrículas não concluídas
            $job_datahora = Carbon::parse($this->matriculas_datahora_fim)->subHours(24);
            if ($job_datahora > now())
                AlertaCandidatosIncompletude::dispatch($this->id, 'Matricula')->delay($job_datahora);
        }
    }

    public function contarSolicitacoesIsencaoTaxaPorAno()
    {
        return SolicitacaoIsencaoTaxa::contarSolicitacoesIsencaoTaxaPorAno($this);
    }

    public function contarSolicitacoesIsencaoTaxaPorMes(int $ano)
    {
        return SolicitacaoIsencaoTaxa::contarSolicitacoesIsencaoTaxaPorMes($ano, $this);
    }

    public function contarInscricoesPorAno()
    {
        return Inscricao::contarInscricoesPorAno($this);
    }

    public function contarInscricoesPorMes(int $ano)
    {
        return Inscricao::contarInscricoesPorMes($ano, $this);
    }

    public function contarMatriculasPorAno()
    {
        return Matricula::contarMatriculasPorAno($this);
    }

    public function contarMatriculasPorMes(int $ano)
    {
        return Matricula::contarMatriculasPorMes($ano, $this);
    }

    /**
     * Retorna os ids das últimas seleções de cada programa, mais o id da última seleção de aluno especial
     */
    public static function obterUltimasSelecoesIds($classe_nome)
    {
        $classe_nome_plural = ClasseUtils::obterClasseNomePlural($classe_nome);

        $ultimasPorPrograma = self::query()->select(DB::raw('MAX(id) AS id'))->whereNotNull('programa_id')->whereNotNull($classe_nome_plural . '_datahora_inicio')->where($classe_nome_plural . '_datahora_inicio', '<=', now())->groupBy('programa_id', 'vinculo_id');
        $ultimaAlunoEspecial = self::query()->select(DB::raw('MAX(id) AS id'))->whereRelation('categoria', 'nome', '=', 'Aluno Especial')->whereNotNull($classe_nome_plural . '_datahora_inicio')->where($classe_nome_plural . '_datahora_inicio', '<=', now())->groupBy('vinculo_id');
        return array_merge($ultimasPorPrograma->pluck('id')->toArray(), $ultimaAlunoEspecial->pluck('id')->toArray());
    }

    public function exigeCategoria()
    {
        $exige_categoria = $this->vinculo?->exige_categoria;
        return (bool) $exige_categoria;
    }

    public function exigePrograma()
    {
        $exige_programa = $this->vinculo?->permite_programa;
        if ($this->exigeCategoria())
            $exige_programa = $this->categoria?->exige_programa;
        return (bool) $exige_programa;
    }

    public function fazInscricoes()
    {
        $faz_inscricoes = $this->vinculo?->fazInscricoes();
        if ($this->exigeCategoria()) {
            $faz_inscricoes = $this->categoria?->fazInscricoes();
            if ($this->exigePrograma())
                $faz_inscricoes = $this->programa?->fazInscricoes();
        }
        return (bool) $faz_inscricoes;
    }

    public function fazMatriculas()
    {
        $faz_matriculas = $this->vinculo?->fazMatriculas();
        if ($this->exigeCategoria()) {
            $faz_matriculas = $this->categoria?->fazMatriculas();
            if ($this->exigePrograma())
                $faz_matriculas = $this->programa?->fazMatriculas();
        }
        return (bool) $faz_matriculas;
    }

    public function permiteTaxa()
    {
        $permite_taxa = $this->vinculo?->permite_taxa;
        return (bool) $permite_taxa;
    }

    public function exigeNivel()
    {
        $exige_nivel = $this->vinculo?->permite_nivel;
        if ($this->exigeCategoria())
            $exige_nivel = $this->categoria?->exige_nivel;
        return (bool) $exige_nivel;
    }

    public function exigeLinhaPesquisa()
    {
        $exige_linhapesquisa = $this->vinculo?->permite_linhapesquisa;
        if ($this->exigeCategoria())
            $exige_linhapesquisa = $this->categoria?->exige_linhapesquisa;
        return (bool) $exige_linhapesquisa;
    }

    public function exigeDisciplinas()
    {
        $exige_disciplinas = $this->vinculo?->permite_disciplinas;
        if ($this->exigeCategoria())
            $exige_disciplinas = $this->categoria?->exige_disciplinas;
        return (bool) $exige_disciplinas;
    }

    public function exigeOrientador()
    {
        $exige_orientador = $this->vinculo?->exige_orientador;
        return (bool) $exige_orientador;
    }

    /**
     * accessor getter para "config"
     */
    public function getConfigAttribute(string $value)
    {
        $value = json_decode($value);

        $out = new \StdClass;
        $out->status = $value->status ?? config('selecoes.config.status');
        return $out;
    }

    /**
     * accessor setter para "config"
     */
    public function setConfigAttribute(string|array $value)
    {
        // quando este método é invocado pelo seeder, $value vem como string JSON
        // quando este método é invocado pelo MVC, $value vem como array

        if (is_string($value)) {
            $value_decoded = json_decode($value, true); // Decodifica como array associativo
            if (is_array($value_decoded) && (json_last_error() == JSON_ERROR_NONE))
                $value = $value_decoded;    // se $value veio como string JSON, vamos utilizar $value_decoded, de modo a poder acessá-lo mais abaixo como array
        }

        $config = new \StdClass;
        $config->status = $value['status'];
        $this->attributes['config'] = json_encode($config);
    }

    /**
     * accessor getter para "template"
     */
    public function getTemplateAttribute(string $value)
    {
        return (empty($value)) ? '{}' : $value;
    }

    /**
     * accessor getter para "linhaspesquisa"
     */
    public function getLinhaspesquisaAttribute()
    {
        return $this->niveislinhaspesquisa->pluck('linhapesquisa')->unique('id')->values();
    }

    /**
     * accessor getter para "responsaveis"
     */
    public function getResponsaveisAttribute()
    {
        $vinculo_id = $this->vinculo_id;

        $funcao_docentes = Funcao::where('nome', 'Docentes do Programa')->whereHas('vinculos', function ($query) use ($vinculo_id) { $query->where('vinculos.id', $vinculo_id); })->first();
        $funcao_secretarios_programa = Funcao::where('nome', 'Secretários(as) do Programa')->whereHas('vinculos', function ($query) use ($vinculo_id) { $query->where('vinculos.id', $vinculo_id); })->first();
        $funcao_coordenadores_programa = Funcao::where('nome', 'Coordenadores(as) do Programa')->whereHas('vinculos', function ($query) use ($vinculo_id) { $query->where('vinculos.id', $vinculo_id); })->first();
        $grupo_funcionarios_setor = Funcao::where('grupo', 'Funcionários(as) do Setor')->whereHas('vinculos', function ($query) use ($vinculo_id) { $query->where('vinculos.id', $vinculo_id); })->first();
        $grupo_coordenadores_setor = Funcao::where('grupo', 'Coordenadores(as) do Setor')->whereHas('vinculos', function ($query) use ($vinculo_id) { $query->where('vinculos.id', $vinculo_id); })->first();

        return [
            [
                'funcao' => 'Docentes do Programa',
                'grupo' => 'Docentes do Programa',
                'users' => ($this->programa && $funcao_docentes) ? $this->programa->obterPessoasFuncao('Docentes do Programa', $vinculo_id) : collect(),
            ],
            [
                'funcao' => 'Secretários(as) do Programa',
                'grupo' => 'Secretários(as) do Programa',
                'users' => ($this->programa && $funcao_secretarios_programa) ? $this->programa->obterPessoasFuncao('Secretários(as) do Programa', $vinculo_id) : collect(),
            ],
            [
                'funcao' => 'Coordenadores(as) do Programa',
                'grupo' => 'Coordenadores(as) do Programa',
                'users' => ($this->programa && $funcao_coordenadores_programa) ? $this->programa->obterPessoasFuncao('Coordenadores(as) do Programa', $vinculo_id) : collect(),
            ],
            [
                'funcao' => $grupo_funcionarios_setor ? $grupo_funcionarios_setor->nome : '',
                'grupo' => 'Funcionários(as) do Setor',
                'users' => $grupo_funcionarios_setor ? $grupo_funcionarios_setor->users()->orderBy('users.name')->get() : collect(),
            ],
            [
                'funcao' => $grupo_coordenadores_setor ? $grupo_coordenadores_setor->nome : '',
                'grupo' => 'Coordenadores(as) do Setor',
                'users' => $grupo_coordenadores_setor ? $grupo_coordenadores_setor->users()->orderBy('users.name')->get() : collect(),
            ],
        ];
    }

    /**
     * uma seleção se relaciona com um vínculo
     */
    public function vinculo()
    {
        return $this->belongsTo('App\Models\Vinculo');
    }

    /**
     * uma seleção se relaciona com uma categoria
     */
    public function categoria()
    {
        return $this->belongsTo('App\Models\Categoria');
    }

    /**
     * uma seleção se relaciona com um programa
     */
    public function programa()
    {
        return $this->belongsTo('App\Models\Programa');
    }

    /**
     * uma seleção se relaciona com n solicitações de isenção de taxa
     */
    public function solicitacoesisencaotaxa()
    {
        return $this->hasMany('App\Models\SolicitacaoIsencaoTaxa');
    }

    /**
     * uma seleção se relaciona com n inscrições
     */
    public function inscricoes()
    {
        return $this->hasMany('App\Models\Inscricao');
    }

    /**
     * uma seleção se relaciona com n matrículas
     */
    public function matriculas()
    {
        return $this->hasMany('App\Models\Matricula');
    }

    /**
     * uma seleção se relaciona com n linhas de pesquisa/temas
     */
    public function niveislinhaspesquisa()
    {
        return $this->belongsToMany('App\Models\NivelLinhaPesquisa', 'selecao_nivellinhapesquisa', 'selecao_id', 'nivellinhapesquisa_id')->withTimestamps();
    }

    /**
     * uma seleção se relaciona com n disciplinas
     */
    public function disciplinas()
    {
        return $this->belongsToMany('App\Models\Disciplina', 'selecao_disciplina', 'selecao_id', 'disciplina_id')->withTimestamps();
    }

    /**
     * uma seleção se relaciona com n motivos de isenção de taxa
     */
    public function motivosisencaotaxa()
    {
        return $this->belongsToMany('App\Models\MotivoIsencaoTaxa', 'selecao_motivoisencaotaxa', 'selecao_id', 'motivoisencaotaxa_id')->withTimestamps();
    }

    /**
     * uma seleção se relaciona com n tipos de arquivo
     */
    public function tiposarquivo()
    {
        return $this->belongsToMany('App\Models\TipoArquivo', 'selecao_tipoarquivo', 'selecao_id', 'tipoarquivo_id')->withTimestamps();
    }

    /**
     * uma seleção se relaciona com n arquivos
     */
    public function arquivos()
    {
        return $this->belongsToMany('App\Models\Arquivo', 'arquivo_selecao')->withPivot('tipo')->withTimestamps();
    }
}
