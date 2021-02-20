@extends('layouts.lis.app')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="{{ url('/admin/home') }}">Home</a>
    </li>
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('content')
  <div class="card card-accent-info align-items-center">
    <div class="header-info">
      <p>
        Informasi {{ date('Y') }}
      </p> 
    </div>
  </div>


@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
<link href="{{ asset('css/cobaslide.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/moze.min.js') }}"></script>
<script src="{{ asset('js/jquery.cslider.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
<script type="text/javascript">
  @if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch(type){
			case 'info':
				toastr.info("{{ Session::get('message') }}");
				break;
			case 'warning':
				toastr.warning("{{ Session::get('message') }}");
				break;
			case 'success':
				toastr.success("{{ Session::get('message') }}");
				break;
			case 'error':
				toastr.error("{{ Session::get('message') }}");
				break;
    }
  @endif

</script>
@endpush