@extends('master')

@section('content')
@parent
  <div class="row">
    <div class="col-md-12 form-inline">
      <span class="h4 mt-2">Solicitação de Isenção de Taxa</span>
      @include('partials.datatable-filter-box', ['otable'=>'oTable'])
    </div>
  </div>

  @if ($vinculos->count() > 0)
    <br />
    Deseja solicitar isenção de taxa para:<br />
    <table class="table table-sm table-hover solicitacao-isencao-taxa display responsive" style="width: 100%;">
      <thead>
        <tr>
          <th style="border: none;"><span class="d-none">Seleções</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($vinculos as $vinculo)
          <tr>
            <td>
              {{ $vinculo->nome }}<br />
              @if ($vinculo->categorias->count() > 0)
                @foreach ($vinculo->categorias as $categoria)
                  @if ($categoria->selecoes->count())
                    <div class="ml-3">
                      {{ $categoria->nome }}
                    </div>
                    @foreach ($categoria->selecoes as $selecao)
                      <div class="ml-5">
                        <a href="solicitacoesisencaotaxa/create/{{ $selecao['id'] }}">{{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif </a>
                      </div>
                    @endforeach
                    <br />
                  @endif
                @endforeach
                <br />
              @else
                @foreach ($vinculo->selecoes as $selecao)
                  <div class="ml-3">
                    <a href="solicitacoesisencaotaxa/create/{{ $selecao['id'] }}">{{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif </a>
                  </div>
                @endforeach
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <br />
    No momento, não há períodos abertos para solicitação de isenção de taxa.
  @endif
@endsection

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {
      oTable = $('.solicitacao-isencao-taxa').DataTable({
        dom:
          't',
          'paging': false,
          'sort': false
      });
    });
  </script>
@endsection
