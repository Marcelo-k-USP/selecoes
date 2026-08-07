@nomenclatura

Olá {{ $candidatonome }},<br />
<br />
O período de matrículas {{ str_starts_with($objetivo, 'aluno especial') ? 'para ' . $objetivo : 'de ' . $objetivo }} encerra-se em {{ formatarDataHora($selecao->matriculas_datahora_fim) }}.<br />
Você iniciou sua matrícula, mas não a enviou.<br />
Entre em sua matrícula, envie todos os documentos exigidos e clique no botão "Enviar Matrícula".<br />
Sem isso, ela não será avaliada!<br />
<br />
@include('emails.rodape')
