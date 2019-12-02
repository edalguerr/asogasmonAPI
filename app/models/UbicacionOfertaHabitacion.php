<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaHabitacion;

class UbicacionOfertaHabitacion extends Model
{
    // 
    protected $table = "ubicacion_oferta_habitacion";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'PAIS',
        'DEPARTAMENTO',
        'CIUDAD',
        'LOCALIDAD',
        'DIRECCION',
        'CODIGO_POSTAL',
        'LATITUD',
        'LONGITUD',
        'OFERTA_HABITACION_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaHabitacion::class,'OFERTA_HABITACION_ID','ID');
    }
}
