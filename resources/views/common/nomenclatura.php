<?php

    $inscricao_ou_matricula = '';
    $objetivo = '';

    if ($selecao->exigePrograma()) {
        if ($selecao->fazInscricoes()) {
            $inscricao_ou_matricula = 'inscrição';
            $objetivo = 'o processo seletivo ' . $selecao->nome;
        } elseif ($selecao->fazMatriculas()) {
            $inscricao_ou_matricula = 'matrícula';
            $objetivo = 'o programa ' . $selecao->programa->nomeCompleto();
        }
    } elseif ($selecao->exigeCategoria()) {
        if ($selecao->categoria->fazInscricoes())
            $inscricao_ou_matricula = 'inscrição';
        elseif ($selecao->categoria->fazMatriculas())
            $inscricao_ou_matricula = 'matrícula';
        $objetivo = 'aluno especial';
    }

    $objetivo .= ' (' . $selecao->vinculo->nome . ')';
