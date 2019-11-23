<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class NormaCasaOfertaHabitacion extends Model
{
    //
    public $incrementing = true;
    protected $table = "norma_casa_has_oferta_habitacion";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'NORMA_CASA_ID',
        'OFERTA_HABITACION_ID'
    ];
}
