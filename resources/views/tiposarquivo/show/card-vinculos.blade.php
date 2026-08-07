@section('styles')
@parent
  <style>
    #card-vinculos {
      border: 1px solid brown;
      border-top: 3px solid brown;
    }
  </style>
@endsection

<a name="card_vinculos"></a>
<div class="card bg-light mb-3" id="card-vinculos">
  <div class="card-header">
    Vínculos
    <span class="badge badge-pill badge-primary">{{ is_null($tipoarquivo->vinculos) ? 0 : $tipoarquivo->vinculos->count() }}</span>
    @can('tiposarquivo.update')
      @include('vinculos.partials.modal-add', ['inclusor_url' => 'tiposarquivo', 'inclusor_objeto' => $tipoarquivo])
    @endcan
  </div>
  <div class="card-body">
    <div class="accordion" id="accordionVinculos">
      @if (!is_null($tipoarquivo->vinculos))
        @foreach ($tipoarquivo->vinculos as $vinculo)
          <div class="card vinculo-item">
            <div class="card-header" style="font-size:15px">
              @include('vinculos.show.header')
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</div>
