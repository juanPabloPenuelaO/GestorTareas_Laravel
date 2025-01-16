@section('title', __('Proyectos'))
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="text-center fas fa-project-diagram"></i> gestión de proyectos</h4>
                        </div>
                        @if (session()->has('message'))
                        <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
                        @endif
                        <div>
                            <input wire:model='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Buscar Proyectos">
                        </div>
                        <div class="btn btn-sm btn-info" data-toggle="modal" data-target="#createDataModal">
                            <i class="fa fa-plus"></i>  Añadir Proyecto
                        </div>
                        <div class="btn btn-sm btn-info" wire:click="prueba()">
                            <i class="fa fa-plus"></i>  Prueba
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @include('livewire.proyectos.create')
                    @include('livewire.proyectos.update')
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr> 
                                    <td>ID</td> 
                                    <th>Nombre Proyecto</th>
                                    <th>Descripción Proyecto</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <td>Acciones</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proyectos as $row)
                                <tr>
                                    <td>{{ $row->id }}</td> 
                                    <td>{{ $row->nombreProyecto }}</td>
                                    <td>{{ $row->descripcionProyecto }}</td>
                                    <td>{{ $row->fecha_inicio }}</td>
                                    <td>{{ $row->fecha_fin }}</td>
                                    <td width="90">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Acciones
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a data-toggle="modal" data-target="#updateModal" class="dropdown-item" wire:click="edit({{ $row->id }})"><i class="fa fa-edit"></i> Editar </a>                             
                                                <a class="dropdown-item" onclick="confirm('Desea eliminar el Proyecto que tiene el ID {{ $row->id }}? \nNo se pueden recuperar')||event.stopImmediatePropagation()" wire:click="destroy({{ $row->id }})"><i class="fa fa-trash"></i> Eliminar </a>   
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>                        
                        {{ $proyectos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
