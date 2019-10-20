<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaCasaApto;

class ServicioEspecifico extends Model
{
    //
    protected $table = "servicio_especifico";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'SERVICIO',
        'ICONO'
    ];

    public function ofertasCasaApto(){
        return $this->belongsToMany(OfertaCasaApto::class,'servicio_especifico_has_oferta_casa_apto',
        'SERVICIO_ESPECIFICO_ID','OFERTA_CASA_APTO_ID','ID','ID')
        ->withPivot('CREADO_EN','ACTUALIZADO_EN');        
    }

}
