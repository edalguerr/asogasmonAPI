<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\SubCategoria;

class Categoria extends Model
{
    //
    protected $table = "categoria";
    const CREATED_AT = 'CREADO_EN';
    const UPDATED_AT = 'ACTUALIZADO_EN';

    protected $fillable = [
        'NOMBRE'
    ];

    public function subCategorias(){
        return $this->hasMany(SubCategoria::class,'CATEGORIA_ID','ID');
    }

}
