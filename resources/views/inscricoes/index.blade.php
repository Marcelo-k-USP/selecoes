@extends('layouts.app')

@section('content')
@parent
  <div class="row">
    <div class="col-md-12 form-inline">
      <div class="d-none d-sm-block h4 mt-2">
        Inscrições
      </div>
      <div class="d-block d-sm-none h4 mt-2">
        {{-- vai mostrar no mobile --}}
        <i class="fas fa-filter"></i>
      </div>
      @if (isset($objetos) && ($objetos->count() > 0))
        <div class="h4 mt-1 ml-2">
          <span class="badge badge-pill badge-primary datatable-counter">-</span>
        </div>
        @include('partials.datatable-filter-box', ['otable' => 'oTable'])
        @canany(['perfiladmin', 'perfilgerente', 'perfildocente'])
          <div class="d-flex align-items-center ml-2" style="gap: 10px;">
            <input type="checkbox" name="somente_da_ultima_selecao" id="somente_da_ultima_selecao" checked="checked" style="width: auto; margin: 0;">
            <label for="somente_da_ultima_selecao" style="margin: 0;">
              Somente da Última Seleção
              @if (auth()->user()?->can('perfiladmin') || in_array(auth()->user()?->grupo_funcao_maximo, ['Funcionários(as) do Setor', 'Coordenadores(as) do Setor']))
                @php
                  $gerencia_algum_programa = auth()->user()?->gerenciaAlgumPrograma();
                  $gerencia_categoria_aluno_especial = auth()->user()?->gerenciaCategoriaAlunoEspecial();
                @endphp
                @if ($gerencia_algum_programa) de cada Programa @endif
                @if ($gerencia_algum_programa && $gerencia_categoria_aluno_especial) e @endif
                @if ($gerencia_categoria_aluno_especial) de Aluno Especial @endif
              @endif
            </label>
          </div>
        @endcanany
      @endif
    </div>
  </div>

  @if (isset($objetos) && ($objetos->count() > 0))
    <table class="table table-striped tabela-inscricoes display responsive" style="width:100%">
      <thead>
        <tr>
          <th>Nro</th>
          <th></th>
          <th>Candidato</th>
          @if (!auth()->user()->gerenciaVinculoUnico())
            <th width="10%">Vínculo</th>
          @endif
          <th>Seleção</th>
          <th>Nível com Linha de Pesquisa/Tema ou Disciplina(s) ou Orientador(a)</th>
          <th width="10%">Criada em</th>
          <th width="10%">Atualização</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($objetos as $inscricao)
          <tr data-is-latest-selecoes="{{ $inscricao->is_latest_selecoes ? '1' : '0' }}">
            <td>
              <a href="inscricoes/edit/{{ $inscricao->id }}">{{ $inscricao->id }}</a>
            </td>
            <td>
              @include('inscricoes.partials.status-small')
            </td>
            <td>
              @php
                $nome = null;
                $extras = null;
                if (!is_null($inscricao->extras)) {
                  $extras = json_decode($inscricao->extras);
                  if ($extras && property_exists($extras, 'nome'))
                    $nome = Str::limit($extras->nome, 32);
                }
              @endphp
              {{ $nome }}
              @include('inscricoes.partials.status-muted')
            </td>
            @if (!auth()->user()->gerenciaVinculoUnico())
              <td>{{ $inscricao->selecao->vinculo->nome }}</td>
            @endif
            <td>
              {{ $inscricao->selecao->nome }}{{ $inscricao->selecao->exigeCategoria() ? ' (' . $inscricao->selecao->categoria->nome . ')' : '' }}
            </td>
            <td>
              @php
                $nivel = null;
                $nivel_nome = null;
                if (!empty($extras->nivel)) {
                  $nivel = json_decode($niveis->firstWhere('id', $extras->nivel));
                  if ($nivel && property_exists($nivel, 'nome'))
                    $nivel_nome = $nivel->nome;
                }
              @endphp
              @if (!is_null($nivel_nome))
                {{ $nivel_nome }} em
              @endif
              @if ($inscricao->selecao->exigeLinhaPesquisa() && !is_null($inscricao->linha_pesquisa))
                {{ $inscricao->linha_pesquisa }}
              @endif
              @if ($inscricao->selecao->exigeDisciplinas() && !is_null($inscricao->disciplinas))
                {!! $inscricao->disciplinas !!}
              @endif
              @if ($inscricao->selecao->exigeOrientador() && !is_null($inscricao->orientador))
                {{ $inscricao->orientador }}
              @endif
            </td>
            <td class="text-right">
              <span class="d-none">{{ $inscricao->created_at }}</span>
              {{ formatarDataHora($inscricao->created_at) }}
            </td>
            <td class="text-right">
              <span class="d-none">{{ $inscricao->updated_at }}</span>
              {{ formatarDataHora($inscricao->updated_at) }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <br />
    Não há nenhuma inscrição {{ auth()->user()->canAny(['perfiladmin', 'perfilgerente', 'perfildocente']) ? '' : 'sua' }} a ser consultada.
  @endif
@stop

@php
  $paginar = (isset($objetos) && ($objetos->count() > 10));
@endphp

@section('javascripts_bottom')
@parent
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.8/css/fixedHeader.dataTables.min.css">
  <script src="https://cdn.datatables.net/fixedheader/3.1.8/js/dataTables.fixedHeader.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {

      if ($('.tabela-inscricoes').length > 0) {
        oTable = $('.tabela-inscricoes').DataTable({
          dom:
            't{{ $paginar ? 'p' : '' }}',
            'paging': {{ $paginar ? 'true' : 'false' }},
            'sort': true,
            'order': [
              [$('.tabela-inscricoes thead th').length - 1, 'desc']    // ordena decrescente pela ultima coluna
            ],
            'fixedHeader': true,
            columnDefs: [{
              targets: 1,
              orderable: false
            }],
            language: {
              url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
            }
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
          if (!$('#somente_da_ultima_selecao').is(':checked'))
              return true;
          return ($(oTable.row(dataIndex).node()).attr('data-is-latest-selecoes') === '1');
        });

        $('#somente_da_ultima_selecao').on('change', function() {
            oTable.draw();
            $('.datatable-counter').html(oTable.page.info().recordsDisplay);
        });

        // recuperando o storage local
        var datatableFilter = localStorage.getItem('datatableFilter');
        $('#dt-search').val(datatableFilter);

        // vamos aplicar o filtro
        oTable.search($('#dt-search').val()).draw();

        // vamos renderizar o contador de linhas
        $('.datatable-counter').html(oTable.page.info().recordsDisplay);

        // vamos guardar no storage à medida que digita
        $('#dt-search').keyup(function() {
          localStorage.setItem('datatableFilter', $(this).val())
        });
      } else
        $('.datatable-counter').html('0');
    });
  </script>
@endsection
