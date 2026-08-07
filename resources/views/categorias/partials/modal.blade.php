<!-- Modal que atende adicionar e editar categorias -->
<div class="modal fade" id="modalForm" data-backdrop="static" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Adicionar/Editar Categorias</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="list_table_div_form">
          {{ html()->form('post', '')->open() }}
          @csrf
          @method('post')
          {{ html()->hidden('id') }}
          @php
            $modo = 'create';
          @endphp
          @foreach ($fields as $col)
            @if (empty($col['type']) || $col['type'] == 'text')
              @include('common.list-table-form-text')
            @elseif ($col['type'] == 'number')
              @include('common.list-table-form-number')
            @elseif ($col['type'] == 'integer')
              @include('common.list-table-form-integer')
            @elseif ($col['type'] == 'select')
              @include('common.list-table-form-select')
            @elseif ($col['type'] == 'checkbox_group')
              <div class="form-group row">
                <label class="col-form-label col-sm-3">{{ $col['label'] }}</label>
                <div class="col-sm-9 d-flex flex-wrap mt-2">
                  @foreach($col['data'] as $name => $label)
                    <div class="form-check form-check-inline mr-4 mb-2">
                      {{ html()->checkbox($name)->class('form-check-input')->id($name) }}
                      <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          @endforeach
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

      $('#modalForm').on('shown.bs.modal', function() {
        $(this).find(':input[type=text]').filter(':visible:first').focus();
      });

      edit_form = function(id) {
        $.get('categorias/' + id
          , function(row) {
            console.log(row);
            // mudando para PUT
            $('#modalForm :input').filter("input[name='_method']").val('PUT');

            // preenchendo o form com os valores a serem editados
            var inputs = $("#modalForm :input").not(":input[type=button], :input[type=submit], :input[type=reset], input[name^='_']");
            inputs.each(function() {
              if (row[this.name] !== undefined) {
                if ($(this).attr('type') === 'checkbox')
                  $(this).prop('checked', ((row[this.name] == 1) || (row[this.name] === true))).trigger('change');
                else
                  $(this).val(row[this.name]).trigger('change');
                console.log(this.name);
              }
            });

            // Ajustando action
            $('#modalForm').find('form').attr('action', 'categorias/' + id);

            // Ajustando o title
            $('#modalLabel').html('Editar Categoria');

            $("#modalForm").modal();
            console.log('inputs', inputs);
          });
      };

      add_form = function(id) {
        $("#modalForm :input").filter("input[type='text']").val('');

        // desmarca os checkboxes
        $("#modalForm :input").filter(":checkbox").prop('checked', false).trigger('change');

        // preenchendo o form com os valores a serem editados
        $("#modalForm select").val(id).trigger('change');

        // Ajustando action
        $('#modalForm').find('form').attr('action', 'categorias');

        $('#modalLabel').html('Adicionar Categoria');
        $('#modalForm :input').filter("input[name='_method']").val('POST');

        $("#modalForm").modal();
      }

      mostraOcultaCampo = function(vinculo, nome) {
        if (vinculo['permite_' + nome])
          $('#exige_' + nome).closest('.form-check').show();
        else
          $('#exige_' + nome).prop('checked', false).closest('.form-check').hide();
      };

      vinculos = @json($vinculos->keyBy('id'));

      $('#vinculo_id').change(function() {
        if ($(this).val() && vinculos[$(this).val()]) {
          vinculo = vinculos[$(this).val()];

          mostraOcultaCampo(vinculo, 'programa');
          mostraOcultaCampo(vinculo, 'nivel');
          mostraOcultaCampo(vinculo, 'linhapesquisa');
          mostraOcultaCampo(vinculo, 'disciplinas');

          if (vinculo.processos) {
            $('#processos').closest('.form-group.row').show();
            $('#processos option').each(function() {
              if ($(this).val()) {
                if (($(this).val() === 'Inscrição e Matrícula') ? (vinculo.processos === 'Inscrição e Matrícula') : (vinculo.processos.indexOf($(this).val()) !== -1))
                  $(this).show().prop('disabled', false);
                else
                  $(this).hide().prop('disabled', true);
              }
            });
            if ($('#processos option:selected').prop('disabled'))
                $('#processos').val('');
          } else
            $('#processos').val('').closest('.form-group.row').hide();

          $('#exige_disciplinas').trigger('change');
        }
      });

      $('#exige_disciplinas').change(function() {
        if ($(this).is(':checked'))
          $('#max_disciplinas').closest('.form-group.row').show();
        else
          $('#max_disciplinas').val('').closest('.form-group.row').hide();
      });
    });
  </script>
@endsection
