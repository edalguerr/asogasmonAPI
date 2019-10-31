<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Categoria;
use App\models\OfertaArticulo;

class SubCategoria extends Model
{
    //
    protected $table = "sub_categoria";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'NOMBRE',
        'CATEGORIA_ID'
    ];

    public function categoria(){
        return $this->belongsTo(Categoria::class,'CATEGORIA_ID','ID');
    }

    public function oferta(){
        return $this->hasMany(OfertaArticulo::class,'SUB_CATEGORIA_ID','ID');
    }
}
