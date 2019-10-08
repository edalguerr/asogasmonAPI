<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    //
    protected $table = 'usuario';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'contrasenia',
        'foto'
    ];

}
