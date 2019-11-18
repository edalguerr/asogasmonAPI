<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\ServicioEspecifico;

class ServicioEspecificoController extends Controller
{
    //
    public function servicios(){
        return response()->json([
            'serviciosEspecificos' => ServicioEspecifico::all()
        ]);
    }
}
