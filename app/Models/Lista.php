<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    protected $table = 'listas'; // Nombre de la tabla en la base de datos

    protected $fillable = ['descripcion', 'numero', 'foto', 'estado'];

    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'idlista');
    }
}





