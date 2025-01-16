<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tarea;
use App\Models\Empleado;
use App\Models\Proyecto;

class Tareas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    
    public $keyWord, $nombreTarea, $descripcionTarea, $plazo_tarea, $proyecto_id, $empleado_id;
    public $updateMode = false;
    public $selected_id;
    public $empleados;
    public $proyectos;

    public function mount()
    {
        $this->traerEmpleados();
        $this->traerProyectos();
    }

    public function render()
    {
      $keyWord = '%' . $this->keyWord . '%';
      return view('livewire.tareas.view', [
        'tareas' => Tarea::with(['empleado', 'proyecto']) ->latest()
          ->orWhere('nombreTarea', 'LIKE', $keyWord)
          ->orWhere('descripcionTarea', 'LIKE', $keyWord)
          ->orWhere('plazo_tarea', 'LIKE', $keyWord)
          ->orWhere('proyecto_id', 'LIKE', $keyWord)
          ->orWhere('empleado_id', 'LIKE', $keyWord)
          ->orWhere('estado', 'LIKE', $keyWord)
          ->paginate(10),
      ]);
    }
    

    public function traerEmpleados()
    {
        $this->empleados = Empleado::all();
    }

    public function traerProyectos()
    {
        $this->proyectos = Proyecto::all();
    }

    public function store()
    {
         $validatedData = $this->validate([
             'nombreTarea' => 'required',
             'descripcionTarea' => 'required',
             'plazo_tarea' => 'required|date',
             'proyecto_id' => 'required',
             'empleado_id' => 'required',
         ]);

        Tarea::create([
            'nombreTarea' => $this->nombreTarea,
            'descripcionTarea' => $this->descripcionTarea,
            'plazo_tarea' => $this->plazo_tarea,
            'proyecto_id' => $this->proyecto_id,
            'empleado_id' => $this->empleado_id,
            'estado' => 1,
        ]);

        $this->resetFields();

        session()->flash('message', 'La tarea se creó.');
    }

    private function resetFields()
    {
        $this->nombreTarea = '';
        $this->descripcionTarea = '';
        $this->plazo_tarea = '';
        $this->proyecto_id = null;
        $this->empleado_id = null;
        $this->selected_id = null;
        $this->updateMode = false;
    }

    public function edit($id){

        $this->updateMode = true;
        $tarea = Tarea::findOrFail($id);

        $this->selected_id = $id;
        $this->nombreTarea = $tarea->nombreTarea;
        $this->descripcionTarea = $tarea->descripcionTarea;
        $this->plazo_tarea = $tarea->plazo_tarea;
        $this->proyecto_id = $tarea->proyecto_id;
        $this->empleado_id = $tarea->empleado_id;
        $this->estado = '1';
    }

    public function update(){
        $validatedData = $this->validate([
            'nombreTarea' => 'required',
            'descripcionTarea' => 'required',
            'plazo_tarea' => 'required|date',
            'proyecto_id' => 'required',
            'empleado_id' => 'required',
        ]);

        if ($this->selected_id) {
            $tarea = Tarea::find($this->selected_id);
            $tarea->update([
                'nombreTarea' => $this->nombreTarea,
                'descripcionTarea' => $this->descripcionTarea,
                'plazo_tarea' => $this->plazo_tarea,
                'proyecto_id' => $this->proyecto_id,
                'empleado_id' => $this->empleado_id,
                'estado' => '1',
            ]);

            $this->resetFields();
            session()->flash('message', 'La tarea se actualizó');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $tarea = Tarea::where('id', $id)->first();
            $tarea->delete();

            session()->flash('message', 'La tarea se eliminó');
        }
    }

    public function cancel()
    {
        $this->resetFields();
        $this->updateMode = false;
    }
}
