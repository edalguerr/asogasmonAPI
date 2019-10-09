<?php

namespace App\models;


use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;


class Usuario extends Model
{
    //
    use HasApiTokens;
    
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
