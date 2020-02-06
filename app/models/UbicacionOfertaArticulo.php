<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaArticulo;

class UbicacionOfertaArticulo extends Model
{
    //
    protected $table = 'ubicacion_oferta_articulo';
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';
    
    protected $fillable = [
        'OFERTA_ARTICULO_ID',
        'PAIS',
        'DEPARTAMENTO',
        'CIUDAD',
        'LOCALIDAD',
        'CODIGO_POSTAL'
    ]; 

    public function oferta(){
        return $this->belongsTo(OfertaArticulo::class,'OFERTA_ARTICULO_ID','ID');
    }
}
