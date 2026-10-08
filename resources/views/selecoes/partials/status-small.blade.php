@if (($selecao->estado == 'Em Elaboração') || str_starts_with($selecao->estado, 'Aguardando Início das') || ($selecao->estado == 'Aguardando Definição do Período de Matrículas'))
  <span class="text-warning" data-toggle="tooltip" title="{{ $selecao->estado }}"> <i class="fas fa-circle"></i> </span>
@elseif (str_starts_with($selecao->estado, 'Período de'))
  <span class="text-success" data-toggle="tooltip" title="{{ $selecao->estado }}"> <i class="fas fa-circle"></i> </span>
@elseif ($selecao->estado == 'Encerrada')
  <span class="text-danger" data-toggle="tooltip" title="{{ $selecao->estado }}"> <i class="fas fa-circle"></i> </span>
@endif
