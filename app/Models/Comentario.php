<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;
	
    public $timestamps = true;

    protected $table = 'comentarios';

    protected $fillable = ['Comentario','nombreCliente', 'fecha_comentario', 'proyecto_id'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
