<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'candidatos';
     
     protected $fillable = [
        'descripcion','foto','idlista','idtipocandidato','estado'
    ];
    public function lista()
    {
        return $this->belongsTo(Lista::class, 'idlista');
    }
    public function tipoCandidato()
    {
        return $this->belongsTo(TipoCandidato::class, 'idtipocandidato');
    }
}
