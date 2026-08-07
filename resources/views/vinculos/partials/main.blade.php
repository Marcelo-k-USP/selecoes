<div class="row">
  <div class="col-md-12 form-inline">
    <span class="h4 mt-2">Vínculos</span>
    @can('vinculos.create')
      &nbsp; &nbsp;
      <button type="button" class="btn btn-sm btn-success" onclick="add_form()">
        <i class="fas fa-plus"></i> Novo
      </button>
      @endcan
  </div>
</div>

<table class="table table-sm my-0 ml-3">
  @foreach ($vinculos as $vinculo)
    {{-- Mostra o conteúdo de um vínculo --}}
    <tr>
      <td>
        <div>
          <a name="{{ \Str::lower($vinculo->id) }}" class="font-weight-bold" style="text-decoration: none;">{{ $vinculo->nome }}</a>
          @can('vinculos.update')
            @include('vinculos.partials.btn-edit')
          @endcan
          @can('vinculos.delete')
            @include('vinculos.partials.btn-delete')
          @endcan
          @include('vinculos.partials.detalhes')
        </div>
      </td>
    </tr>
  @endforeach
</table>
