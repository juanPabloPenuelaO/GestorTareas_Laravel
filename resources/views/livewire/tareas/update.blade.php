<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Editar Tarea</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span wire:click.prevent="cancel()" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="selected_id">
                    <div class="form-group">
                        <label for="nombreTarea">Nombre de la Tarea</label>
                        <input wire:model="nombreTarea" type="text" class="form-control" id="nombreTarea" placeholder="Nombre de la Tarea">
                        @error('nombreTarea') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="descripcionTarea">Descripción de la Tarea</label>
                        <textarea wire:model="descripcionTarea" class="form-control" id="descripcionTarea" placeholder="Descripción de la Tarea"></textarea>
                        @error('descripcionTarea') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="plazo_tarea">Plazo de la Tarea</label>
                        <input wire:model="plazo_tarea" type="date" class="form-control" id="plazo_tarea">
                        @error('plazo_tarea') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="proyecto_id">Proyecto</label>
                        <select wire:model="proyecto_id" class="form-control" id="proyecto_id">
                            <option value="">Selecciona el Proyecto de esta tarea</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombreProyecto }}</option>
                            @endforeach
                        </select>
                        @error('proyecto_id') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="empleado_id">Empleado</label>
                        <select wire:model="empleado_id" class="form-control" id="empleado_id">
                            <option value="">Selecciona el Empleado Encargado</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empleado_id') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" wire:click.prevent="update()" class="btn btn-primary" data-dismiss="modal">Guardar</button>
            </div>
       </div>
    </div>
</div>
