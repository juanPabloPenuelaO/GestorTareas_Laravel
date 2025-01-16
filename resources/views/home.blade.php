@extends('layouts.app')
@section('title', __('Principal'))
@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header"><h5><span class="text-center fa fa-home"></span> @yield('title')</h5></div>
			<div class="card-body">
				<h5>Buenos días <strong>{{ Auth::user()->name }},</strong> {{ __('Prueba en ') }}{{ config('app.name', 'Ingesoftsi') }}</h5>
				</br> 
				<hr>
								
			<div class="row w-100">
					<div class="col-md-3">
						<div class="card border-info mx-sm-1 p-3">
							<div class="card border-info text-info p-3" ><span class="text-center fa fa-plane-departure" aria-hidden="true"></span></div>
							<div class="text-info text-center mt-3"><h4>Proyectos</h4></div>
							<div class="text-info text-center mt-2"><h1>4</h1></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="card border-success mx-sm-1 p-3">
							<div class="card border-success text-success p-3 my-card"><span class="text-center fa fa-luggage-cart" aria-hidden="true"></span></div>
							<div class="text-success text-center mt-3"><h4>Tareas</h4></div>
							<div class="text-success text-center mt-2"><h1>9</h1></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="card border-danger mx-sm-1 p-3">
							<div class="card border-danger text-danger p-3 my-card" ><span class="text-center fa fa-person-booth" aria-hidden="true"></span></div>
							<div class="text-danger text-center mt-3"><h4>comentarios</h4></div>
							<div class="text-danger text-center mt-2"><h1>3</h1></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="card border-warning mx-sm-1 p-3">
							<div class="card border-warning text-warning p-3 my-card" ><span class="text-center fa fa-users" aria-hidden="true"></span></div>
							<div class="text-warning text-center mt-3"><h4>Users</h4></div>
							<div class="text-warning text-center mt-2"><h1>{{ Auth::user()->count() }}</h1></div>
						</div>
					</div>
				 </div>				
			</div>
		</div>
	</div>
</div>
</div>
@endsection