@section('title', __('Tareas'))
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="text-center fas fa-calendar-check"></i> gestion de tareas</h4>
                        </div>
                        @if (session()->has('message'))
                        <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                        @endif
                        <div>
                            <input wire:model='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Buscar Tareas">
                        </div>
                        <div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
                            <i class="fa fa-plus"></i>  Añadir Tarea
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @include('livewire.tareas.create')
                    @include('livewire.tareas.update')
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr> 
                                    <td>ID</td> 
                                    <th>Nombre Tarea</th>
                                    <th>Descripción Tarea</th>
                                    <th>Plazo Tarea</th>
                                    <th>Proyecto</th>
                                    <th>Encargado</th>
                                    <th>Estado</th>
                                    <td>Acciones</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tareas as $row)
                                <tr>
                                    <td>{{ $row->id }}</td> 
                                    <td>{{ $row->nombreTarea }}</td>
                                    <td>{{ $row->descripcionTarea }}</td>
                                    <td>{{ $row->plazo_tarea }}</td>
                                    <td>{{ $row->proyecto->nombreProyecto }}</td>
                                    <td>{{ $row->empleado->nombre }}</td>
                                    <td>{{ $row->estado }}</td>
                                    <td width="90">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Acciones
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a data-toggle="modal" data-target="#updateModal" class="dropdown-item" wire:click="edit({{ $row->id }})"><i class="fa fa-edit"></i> Editar </a>                         
                                                <a class="dropdown-item" onclick="confirm('Desea eliminar la Tarea que tiene el id {{ $row->id }}? \nNo se pueden recuperar')||event.stopImmediatePropagation()" wire:click="destroy({{ $row->id }})"><i class="fa fa-trash"></i> Eliminar </a>   
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>                        
                        {{ $tareas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
