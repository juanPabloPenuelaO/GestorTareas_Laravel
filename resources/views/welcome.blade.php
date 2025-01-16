@extends('layouts.app')
@section('title', __('Ingesoftsi'))
@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5><span class="text-center fa fa-home"></span> @yield('title')</h5></div>
            <div class="card-body">
              <h5>  
            @guest
				
				{{ __('bienvenido a ') }} {{ config('app.name', 'Laravel') }} ! <br></br>
				Inicie sesión para continuar o registrate si no lo has hecho.
            
            
			@else
					Buenos días {{ Auth::user()->name }}, bienvenido a la prueba en {{ config('app.name', 'Laravel') }}.
            @endif	
				</h5>
            </div>
        </div>
    </div>
</div>
</div>
@endsection