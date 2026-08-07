<div class="d-flex">
  <b>
    {{ $vinculo->nome }}
  </b>
  <div class="hidden-btn d-none ml-auto">
    @can('tiposarquivo.update')
      @include('common.btn-delete-sm', ['action' => "tiposarquivo/{$tipoarquivo->id}/vinculos/{$vinculo->id}"])
    @endcan
  </div>
</div>

@once
@section('javascripts_bottom')
@parent
  <script type="text/javascript">
    $(function() {
      $('.vinculo-item').hover(
        function() {
          $(this).find('.hidden-btn').removeClass('d-none');
        },
        function() {
          $(this).find('.hidden-btn').addClass('d-none');
        }
      );
    });
  </script>
@endsection
@endonce
