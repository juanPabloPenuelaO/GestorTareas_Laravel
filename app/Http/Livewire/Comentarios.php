<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Comentario;
use App\Models\Proyecto;

class Comentarios extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord = '', $Comentario, $nombreCliente, $fecha_comentario, $proyecto_id;
    public $updateMode = false;
    public $proyectos;


    public function mount()
    {
        $this->traerProyectos();
    }


    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';
        return view('livewire.comentarios.view', [
            'comentarios' => Comentario::latest()
                ->orWhere('comentario', 'LIKE', $keyWord)
                ->orWhere('nombreCliente', 'LIKE', $keyWord)
                ->orWhere('fecha_comentario', 'LIKE', $keyWord)
                ->orWhere('proyecto_id', 'LIKE', $keyWord)
                ->paginate(10),
        ]);
    }

    public function traerProyectos()
    {
        $this->proyectos = Proyecto::all();
    }

    public function prueba()
    {
        dd('Prueba exitosa');
    }

    public function store()
    {
        $this->validate([
            'Comentario' => 'required',
            'nombreCliente' => 'required',
            'fecha_comentario' => 'required|date|before_or_equal:today',
            'proyecto_id' => 'required',
        ]);

        Comentario::create([
            'Comentario' => $this->comentario,
            'nombreCliente' => $this->nombreCliente,
            'fecha_comentario' => $this->fecha_comentario,
            'proyecto_id' => $this->proyecto_id,
        ]);

        $this->resetAll();
        session()->flash('message', 'El Comentario se subió');
    }

    private function resetAll()
    {
        $this->comentario = '';
        $this->nombreCliente = '';
        $this->fecha_comentario = '';
        $this->proyecto_id = '';
        $this->selected_id = null;
        $this->updateMode = false;
    }

    public function edit($id)
    {
        $this->updateMode = true;
        $comentario = Comentario::findOrFail($id);

        $this->selected_id = $id;
        $this->comentario = $comentario->Comentario;
        $this->nombreCliente = $comentario->nombreCliente;
        $this->fecha_comentario = $comentario->fecha_comentario;
        $this->proyecto_id = $comentario->proyecto_id;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'Comentario' => 'required',
            'nombreCliente' => 'required',
            'fecha_comentario' => 'required|date|before_or_equal:today',
            'proyecto_id' => 'required',
        ]);

        if ($this->selected_id) {
            $comentario = Comentario::find($this->selected_id);
            $comentario->update($validatedData);

            $this->resetAll();
            session()->flash('message', 'El comentario se actualizó');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            Comentario::where('id', $id)->first()->delete();
            session()->flash('message', 'El comentario se eliminó');
        }
    }

    public function cancel()
    {
        $this->resetAll();
        $this->updateMode = false;
    }
}
