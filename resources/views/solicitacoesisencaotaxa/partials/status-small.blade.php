@if (in_array($solicitacaoisencaotaxa->estado, ['Aguardando Envio', 'Em Avaliação']))
  <span class="text-warning" data-toggle="tooltip" title="{{ $solicitacaoisencaotaxa->estado }}"> <i class="fas fa-circle"></i> </span>
@elseif (in_array($solicitacaoisencaotaxa->estado, ['Enviada', 'Aprovada', 'Aprovada Após Recurso']))
  <span class="text-success" data-toggle="tooltip" title="{{ $solicitacaoisencaotaxa->estado }}"> <i class="fas fa-circle"></i> </span>
@elseif (in_array($solicitacaoisencaotaxa->estado, ['Rejeitada']))
  <span class="text-danger" data-toggle="tooltip" title="{{ $solicitacaoisencaotaxa->estado }}"> <i class="fas fa-circle"></i> </span>
@endif
