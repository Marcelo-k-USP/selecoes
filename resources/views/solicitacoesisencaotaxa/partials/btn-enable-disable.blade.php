@section('styles')
@parent
  {{-- https://stackoverflow.com/questions/50349017/how-can-i-change-cursor-for-disabled-button-or-a-in-bootstrap-4 --}}
  <style>
    button:disabled {
      cursor: not-allowed;
      pointer-events: all !important;
    }
</style>
@endsection

{{ html()->form('post', 'solicitacoesisencaotaxa/edit/' . $solicitacaoisencaotaxa->id)->open() }}
  @method('put')
  @csrf
  <input type="hidden" name="conjunto_alterado" id="conjunto_alterado" value="estado">
  <div class="btn-group btn-enable-disable">
    <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Aguardando Envio') ? 'btn-warning' : 'btn-secondary' }}" disabled name="estado" value="Aguardando Envio">
      Aguardando Envio
    </button>
    @if ($solicitacaoisencaotaxa->estado != 'Aguardando Envio')
      <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Enviada') ? 'btn-success' : 'btn-secondary' }}" disabled name="estado" value="Enviada">
        Enviada
      </button>
    @endif
    @if ($solicitacaoisencaotaxa->estado != 'Aguardando Envio')
      <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Em Avaliação') ? 'btn-warning' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || !in_array($solicitacaoisencaotaxa->estado, ['Enviada', 'Aprovada', 'Rejeitada'])) disabled @endif name="estado" value="Em Avaliação">
        Em Avaliação
      </button>
    @endif
    @if (in_array($solicitacaoisencaotaxa->estado, ['Em Avaliação', 'Aprovada']))
      <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Aprovada') ? 'btn-success' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($solicitacaoisencaotaxa->estado != 'Em Avaliação')) disabled @endif name="estado" value="Aprovada">
        Aprovada
      </button>
    @endif
    @if (in_array($solicitacaoisencaotaxa->estado, ['Em Avaliação', 'Rejeitada', 'Aprovada Após Recurso']))
      <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Rejeitada') ? 'btn-danger' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($solicitacaoisencaotaxa->estado != 'Em Avaliação')) disabled @endif name="estado" value="Rejeitada">
        Rejeitada
      </button>
    @endif
    @if (in_array($solicitacaoisencaotaxa->estado, ['Rejeitada', 'Aprovada Após Recurso']))
      <button type="submit" class="btn btn-sm {{ ($solicitacaoisencaotaxa->estado == 'Aprovada Após Recurso') ? 'btn-success' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($solicitacaoisencaotaxa->estado != 'Rejeitada')) disabled @endif name="estado" value="Aprovada Após Recurso">
        Aprovada Após Recurso
      </button>
    @endif
  </div>
{{ html()->form()->close() }}

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {

      @if (in_array($solicitacaoisencaotaxa->estado, ['Aprovada', 'Rejeitada']))
        $('button[name="estado"][value="Em Avaliação"]').on('click', function(e) {
            if (!confirm('Tem certeza que deseja RETROCEDER o estado da solicitação de isenção de taxa?'))
              e.preventDefault();
        });
      @endif
    });
  </script>
@endsection
