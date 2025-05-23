<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'proyectos';

    protected $fillable = ['nombreProyecto','descripcionProyecto', 'fecha_inicio', 'fecha_fin'];
}
