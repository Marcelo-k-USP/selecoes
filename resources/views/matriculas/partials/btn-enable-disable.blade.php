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

{{ html()->form('post', $data->url . '/edit/' . $matricula->id)->open() }}
  @method('put')
  @csrf
  <input type="hidden" name="conjunto_alterado" id="conjunto_alterado" value="estado">
  <div class="btn-group btn-enable-disable">
    <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Aguardando Envio') ? 'btn-warning' : 'btn-secondary' }}" disabled name="estado" value="Aguardando Envio">
      Aguardando Envio
    </button>
    @if ($matricula->estado != 'Aguardando Envio')
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Enviada') ? 'btn-success' : 'btn-secondary' }}" disabled name="estado" value="Enviada">
        Enviada
      </button>
    @endif
    @if ($matricula->estado != 'Aguardando Envio')
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Em Pré-Avaliação') ? 'btn-warning' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || !in_array($matricula->estado, ['Enviada', 'Pré-Aprovada', 'Pré-Rejeitada'])) disabled @endif name="estado" value="Em Pré-Avaliação">
        Em Pré-Avaliação
      </button>
    @endif
    @if (in_array($matricula->estado, ['Em Pré-Avaliação', 'Pré-Aprovada', 'Em Avaliação', 'Aprovada', 'Rejeitada']))
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Pré-Aprovada') ? 'btn-success' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($matricula->estado != 'Em Pré-Avaliação')) disabled @endif name="estado" value="Pré-Aprovada">
        Pré-Aprovada
      </button>
    @endif
    @if (in_array($matricula->estado, ['Em Pré-Avaliação', 'Pré-Rejeitada']))
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Pré-Rejeitada') ? 'btn-danger' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($matricula->estado != 'Em Pré-Avaliação')) disabled @endif name="estado" value="Pré-Rejeitada">
        Pré-Rejeitada
      </button>
    @endif
    @if (in_array($matricula->estado, ['Pré-Aprovada', 'Em Avaliação', 'Aprovada', 'Rejeitada']))
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Em Avaliação') ? 'btn-warning' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || !in_array($matricula->estado, ['Pré-Aprovada', 'Aprovada', 'Rejeitada'])) disabled @endif name="estado" value="Em Avaliação">
        Em Avaliação
      </button>
    @endif
    @if (in_array($matricula->estado, ['Em Avaliação', 'Aprovada']))
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Aprovada') ? 'btn-success' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($matricula->estado != 'Em Avaliação')) disabled @endif name="estado" value="Aprovada">
        Aprovada
      </button>
    @endif
    @if (in_array($matricula->estado, ['Em Avaliação', 'Rejeitada']))
      <button type="submit" class="btn btn-sm {{ ($matricula->estado == 'Rejeitada') ? 'btn-danger' : 'btn-secondary' }}" @if ((session('perfil') == 'usuario') || ($matricula->estado != 'Em Avaliação')) disabled @endif name="estado" value="Rejeitada">
        Rejeitada
      </button>
    @endif
  </div>
{{ html()->form()->close() }}

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {

      @if (in_array($matricula->estado, ['Pré-Aprovada', 'Pré-Rejeitada']))
        $('button[name="estado"][value="Em Pré-Avaliação"]').on('click', function(e) {
            if (!confirm('Tem certeza que deseja RETROCEDER o estado da matrícula?'))
              e.preventDefault();
        });
      @endif

      @if (in_array($matricula->estado, ['Aprovada', 'Rejeitada']))
        $('button[name="estado"][value="Em Avaliação"]').on('click', function(e) {
            if (!confirm('Tem certeza que deseja RETROCEDER o estado da matrícula?'))
              e.preventDefault();
        });
      @endif
    });
  </script>
@endsection
