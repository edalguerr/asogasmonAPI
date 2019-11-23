<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaHabitacion;

class FotoHabitacion extends Model
{
    //
    protected $table = "foto_habitacion";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'FOTO',
        'OFERTA_HABITACION_ID'
    ];

    public function oferta(){
        return $this->belongsTo(OfertaHabitacion::class,'OFERTA_HABITACION_ID','ID');
    }

}
