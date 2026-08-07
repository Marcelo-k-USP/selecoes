<!-- Modal que atende adicionar e editar vínculos -->
<div class="modal fade" id="modalForm" data-backdrop="static" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Adicionar/Editar Vínculos</h5>
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
              @elseif ($col['type'] == 'checkbox')
                @include('common.list-table-form-checkbox')
              @elseif ($col['type'] == 'radio')
                @include('common.list-table-form-radio')
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

      var modalForm = $('#modalForm');

      $('#modalForm').on('shown.bs.modal', function() {
        $(this).find(':input[type=text]').filter(':visible:first').focus();
      })

      edit_form = function(id) {
        $.get('vinculos/' + id, function(row) {
          console.log(row);
          // mudando para PUT
          $('#modalForm :input').filter("input[name='_method']").val('PUT');

          // preenchendo o form com os valores a serem editados
          var inputs = $("#modalForm :input").not(":input[type=button], :input[type=submit], :input[type=reset], input[name^='_']");
          inputs.each(function() {
            if (row[this.name] !== undefined) {
              if ($(this).attr('type') === 'radio') {
                  if ($(this).val() === String(row[this.name]))
                      $(this).prop('checked', true).trigger('change');
              } else if ($(this).attr('type') === 'checkbox')
                  $(this).prop('checked', ((row[this.name] == 1) || (row[this.name] === true))).trigger('change');
              else {
                $(this).val(row[this.name]).trigger('change');
                if ($(this).attr('oninput') == 'validateNumber(this)')
                  $(this).val(formatarDecimal($(this).val()));
              }
            }
          });

          // Ajustando action
          $('#modalForm').find('form').attr('action', 'vinculos/' + id);

          // Ajustando o title
          $('#modalLabel').html('Editar Vínculo');

          $("#modalForm").modal();
          console.log('inputs', inputs);
        });
      }

      add_form = function() {
        // reset de todos os campos do formulário
        $('#modalForm').find('form')[0].reset();

        // força os listeners a esconder os campos opcionais logo ao abrir o modal limpo
        $('#modalForm :input').trigger('change');

        // Ajustando action
        $('#modalForm').find('form').attr('action', 'vinculos');

        $('#modalLabel').html('Adicionar Vínculo');
        $('#modalForm :input').filter("input[name='_method']").val('POST');

        $("#modalForm").modal();
      }

      $('#processos').change(function() {
        if ($(this).val() && $(this).val().indexOf('Inscrição') !== -1)
          $('#link_inscricao_termos').closest('.form-group.row').show();
        else
          $('#link_inscricao_termos').val('').closest('.form-group.row').hide();
      }).trigger('change');

      $('#permite_taxa').change(function() {
        if (!$(this).is(':checked')) {
          $('#boleto_codigo_fonte_recurso').val('').closest('.form-group.row').hide();
          $('#boleto_estrutura_hierarquica').val('').closest('.form-group.row').hide();
          $('input[name="boleto_momento_envio"]').prop('checked', false).closest('.form-group.row').hide();
        } else {
          $('#boleto_codigo_fonte_recurso').closest('.form-group.row').show();
          $('#boleto_estrutura_hierarquica').closest('.form-group.row').show();
          $('input[name="boleto_momento_envio"]').closest('.form-group.row').show();
        }
      }).trigger('change');
    });
</script>
@endsection
