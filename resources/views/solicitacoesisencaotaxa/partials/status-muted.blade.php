@if ($solicitacaoisencaotaxa->estado == 'Aguardando Envio')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@elseif ($solicitacaoisencaotaxa->estado == 'Enviada')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@elseif ($solicitacaoisencaotaxa->estado == 'Em Avaliação')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@elseif ($solicitacaoisencaotaxa->estado == 'Aprovada')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@elseif ($solicitacaoisencaotaxa->estado == 'Rejeitada')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@elseif ($solicitacaoisencaotaxa->estado == 'Aprovada Após Recurso')
  <span class="badge badge-light text-secondary"> {{ $solicitacaoisencaotaxa->estado }} </span>
@endif
