<div class="container mt-5">
    <h2>Agregar Comentario</h2>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="store">
        <div class="mb-3">
            <label for="Comentario" class="form-label">Comentario</label>
            <input type="text" wire:model="Comentario" class="form-control" id="Comentario" required>
            @error('Comentario') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="nombreCliente" class="form-label">Nombre del Cliente</label>
            <input type="text" wire:model="nombreCliente" class="form-control" id="nombreCliente" required>
            @error('nombreCliente') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="fecha_comentario" class="form-label">Fecha</label>
            <input type="date" wire:model="fecha_comentario" class="form-control" id="fecha_comentario" required>
            @error('fecha_comentario') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="proyecto_id" class="form-label">Proyecto ID</label>
            <input type="text" wire:model="proyecto_id" class="form-control" id="proyecto_id" required>
            @error('proyecto_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" wire:click="resetAll">Cancelar</button>
    </form>
</div>
