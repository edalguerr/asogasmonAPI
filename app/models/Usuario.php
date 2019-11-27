<?php

namespace App\models;


use Illuminate\Database\Eloquent\Model;
use App\models\OfertaArticulo;
use App\models\OfertaCasaApto;
use App\models\OfertaHabitacion;
use App\models\OfertaPension;

class Usuario extends Model
{
    //
    
    protected $table = 'usuario';
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';
    
    protected $fillable = [
        'NOMBRE',
        'APELLIDOS',
        'EMAIL',
        'CONTRASENIA',
        'FOTO',
        'ID_TOKEN'
    ];

    public function ofertasArticulo(){
        return $this->hasMany(OfertaArticulo::class,'USUARIO_ID','ID');
    }

    public function ofertasCasaApto(){
        return $this->hasMany(OfertaCasaApto::class,'USUARIO_ID','ID');
    }

    public function ofertasHabitacion(){
        return $this->hasMany(OfertaHabitacion::class,'USUARIO_ID','ID');
    }

    public function ofertasPension(){
        return $this->hasMany(OfertaPension::class,'USUARIO_ID','ID');
    }
    
}
