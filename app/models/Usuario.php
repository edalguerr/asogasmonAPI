<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\OfertaArticulo;

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

}
