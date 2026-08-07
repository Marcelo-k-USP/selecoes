<button type="button" class="btn btn-sm btn-light text-primary" data-toggle="modal" data-target="#VinculoModal">
  <i class="fas fa-plus"></i> Adicionar
</button>

<!-- Modal -->
<div class="modal fade" id="VinculoModal" data-backdrop="static" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Adicionar Vínculo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="list_table_div_form">
          {{ html()->form('post', $inclusor_url . '/' . $inclusor_objeto->id . '/vinculos')->open() }}
            @csrf
            @method('post')
            {{ html()->hidden('id') }}
            <div class="form-group row">
              <div class="col-form-label col-sm-3">Vínculo</div>
              <div class="col-sm-8">
                <select class="form-control" name="id" id="id_campo1">
                  @foreach ($vinculos as $vinculo)
                    <option value='{{ $vinculo->id }}'>{{ $vinculo->nome }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="text-right">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
          {{ html()->form()->close() }}
        </div>
      </div>
    </div>
  </div>
</div>

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {

      $('#VinculoModal').on('shown.bs.modal', function() {
        $('#id_campo1').focus();
      });

      add_modal_form = function() {
        $('#VinculoModal').modal();
      };
    });
  </script>
@endsection
