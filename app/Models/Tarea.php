<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'tareas';

    protected $fillable = ['nombreTarea','descripcionTarea', 'plazo_tarea', 'proyecto_id', 'empleado_id', 'estado'];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
    
}
