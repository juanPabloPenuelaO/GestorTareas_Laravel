<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proyecto;

class Proyectos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $nombreProyecto, $descripcionProyecto, $fecha_inicio, $fecha_fin;
    public $updateMode = false;

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return view('livewire.proyectos.view', [
            'proyectos' => Proyecto::latest()
                        ->orWhere('nombreProyecto', 'LIKE', $keyWord)
                        ->orWhere('descripcionProyecto', 'LIKE', $keyWord)
                        ->orWhere('fecha_inicio', 'LIKE', $keyWord)
                        ->orWhere('fecha_fin', 'LIKE', $keyWord)
                        ->paginate(10),
        ]);
    }

    public function prueba()
    {
        dd('Prueba exitosa');
    }

    public function store(){
        $validatedData = $this->validate([
            'nombreProyecto' => 'required',
            'descripcionProyecto' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Proyecto::create([
            'nombreProyecto' => $this->nombreProyecto,
            'descripcionProyecto' => $this->descripcionProyecto,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ]);

        $this->resetAll();

        session()->flash('message', 'El proyecto se creó');
    }

    private function resetAll(){
        $this->nombreProyecto = '';
        $this->descripcionProyecto = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->selected_id = null;
        $this->updateMode = false;
    }

    public function edit($id){
        $this->updateMode = true;
        $proyecto = Proyecto::findOrFail($id);

        $this->selected_id = $id;
        $this->nombreProyecto = $proyecto->nombreProyecto;
        $this->descripcionProyecto = $proyecto->descripcionProyecto;
        $this->fecha_inicio = $proyecto->fecha_inicio;
        $this->fecha_fin = $proyecto->fecha_fin;
    }

    public function update(){
        $validatedData = $this->validate([
            'nombreProyecto' => 'required',
            'descripcionProyecto' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        if ($this->selected_id) {
            $proyecto = Proyecto::find($this->selected_id);
            $proyecto->update([
                'nombreProyecto' => $this->nombreProyecto,
                'descripcionProyecto' => $this->descripcionProyecto,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
            ]);

            $this->resetAll();
            session()->flash('message', 'El proyecto se actualizó');
        }
    }

    public function destroy($id){
        if ($id) {
            $proyecto = Proyecto::where('id', $id)->first();
            $proyecto->delete();

            session()->flash('message', 'El proyecto se eliminó');
        }
    }

    public function cancel(){
        $this->resetAll();
        $this->updateMode = false;
    }


}
