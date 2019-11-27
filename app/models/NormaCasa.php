<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaHabitacion;
use App\models\OfertaPension;

class NormaCasa extends Model
{
    //
    protected $table = "norma_casa";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'NORMA'
    ];

    public function ofertasHabitacion(){
        return $this->belongsToMany(OfertaHabitacion::class,'norma_casa_has_oferta_habitacion',
        'NORMA_CASA_ID','OFERTA_HABITACION_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');        
    }

    public function ofertasPension(){
        return $this->belongsToMany(OfertaPension::class,'norma_casa_has_oferta_pension',
        'NORMA_CASA_ID','OFERTA_PENSION_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');        
    }

}
