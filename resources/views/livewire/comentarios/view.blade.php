@section('title', __('Comentarios'))
<div class="container mt-5">
    <h2>Comentarios</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3">
        <input type="text" wire:model="keyWord" class="form-control" placeholder="Buscar Comentarios...">
    </div>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Comentario</th>
                <th>Nombre del Cliente</th>
                <th>Fecha</th>
                <th>Proyecto ID</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comentarios as $comentario)
                <tr>
                    <td>{{ $comentario->Comentario }}</td>
                    <td>{{ $comentario->nombreCliente }}</td>
                    <td>{{ $comentario->fecha_comentario }}</td>
                    <td>{{ $comentario->proyecto->nombreProyecto}}</td>
                    <td>
                        <button class="btn btn-info" wire:click="edit({{ $comentario->id }})">Editar</button>
                        <button class="btn btn-danger" wire:click="destroy({{ $comentario->id }})">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $comentarios->links() }}
    </div>

    @if($updateMode)
        <div class="mt-4">
            <h3>Editar Comentario</h3>
            <form wire:submit.prevent="update">
                <div class="mb-3">
                    <input type="text" wire:model="Comentario" class="form-control" placeholder="Comentario" required>
                </div>
                <div class="mb-3">
                    <input type="text" wire:model="nombreCliente" class="form-control" placeholder="Nombre del Cliente" required>
                </div>
                <div class="mb-3">
                    <input type="date" wire:model="fecha_comentario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <select wire:model="proyecto_id" class="form-control" required>
                        <option value="">Seleccione un Proyecto</option>
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombreProyecto }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Actualizar</button>
                <button type="button" class="btn btn-secondary" wire:click="cancel">Cancelar</button>
            </form>
        </div>
    @else
        <div class="mt-4">
            <h3>Agregar Comentario</h3>
            <form wire:submit.prevent="store">
                <div class="mb-3">
                    <input type="text" wire:model="Comentario" class="form-control" placeholder="Comentario" required>
                </div>
                <div class="mb-3">
                    <input type="text" wire:model="nombreCliente" class="form-control" placeholder="Nombre del Cliente" required>
                </div>
                <div class="mb-3">
                    <input type="date" wire:model="fecha_comentario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <select wire:model="proyecto_id" class="form-control" required>
                        <option value="">Seleccione un Proyecto</option>
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombreProyecto }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    @endif
</div>
