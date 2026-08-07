@extends('master')

@section('content')
@parent
  <div class="row">
    <div class="col-md-12 form-inline">
      <span class="h4 mt-2">Nova Inscrição</span>
      @include('partials.datatable-filter-box', ['otable'=>'oTable'])
    </div>
  </div>

  @if ($vinculos->count() > 0)
    <br />
    Deseja se inscrever para:<br />
    <table class="table table-sm table-hover nova-inscricao display responsive" style="width: 100%;">
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
                      @if ($selecao->exigeNivel())
                        <div class="ml-5">
                          {{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif <br />
                          @foreach ($selecao->niveis as $nivel)
                            &nbsp; &nbsp; &nbsp;<a href="inscricoes/create/{{ $selecao['id'] }}/{{ $nivel->id }}">{{ $nivel->nome }}</a><br />
                          @endforeach
                        </div>
                      @else
                        <div class="ml-5">
                          <a href="inscricoes/create/{{ $selecao['id'] }}">{{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif </a>
                        </div>
                      @endif
                    @endforeach
                    <br />
                  @endif
                @endforeach
              @else
                @foreach ($vinculo->selecoes as $selecao)
                  @if ($selecao->exigeNivel())
                    <div class="ml-3">
                      {{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif <br />
                      @foreach ($selecao->niveis as $nivel)
                        &nbsp; &nbsp; &nbsp;<a href="inscricoes/create/{{ $selecao['id'] }}/{{ $nivel->id }}">{{ $nivel->nome }}</a><br />
                      @endforeach
                    </div>
                  @else
                    <div class="ml-3">
                      <a href="inscricoes/create/{{ $selecao['id'] }}">{{ $selecao->nome }} @if (!is_null($selecao->descricao)) - {{ $selecao->descricao }} @endif </a>
                    </div>
                  @endif
                @endforeach
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <br />
    No momento, não há períodos abertos para inscrições.
  @endif
@endsection

@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(document).ready(function() {
      oTable = $('.nova-inscricao').DataTable({
        dom:
          't',
          'paging': false,
          'sort': false
      });
    });
  </script>
@endsection
