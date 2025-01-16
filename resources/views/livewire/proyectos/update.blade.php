<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Editar Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span wire:click.prevent="cancel()" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="selected_id">
                    <div class="form-group">
                        <label for="nombreProyecto">Nombre del Proyecto</label>
                        <input wire:model="nombreProyecto" type="text" class="form-control" id="nombreProyecto" placeholder="Nombre del Proyecto">
                        @error('nombreProyecto') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="descripcionProyecto">Descripción del Proyecto</label>
                        <textarea wire:model="descripcionProyecto" class="form-control" id="descripcionProyecto" placeholder="Descripción del Proyecto"></textarea>
                        @error('descripcionProyecto') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input wire:model="fecha_inicio" type="date" class="form-control" id="fecha_inicio">
                        @error('fecha_inicio') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin</label>
                        <input wire:model="fecha_fin" type="date" class="form-control" id="fecha_fin">
                        @error('fecha_fin') <span class="error text-danger">{{ $message }}</span> @enderror
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
