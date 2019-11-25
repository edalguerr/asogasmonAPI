<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\NormaCasa;

class NormaCasaController extends Controller
{
    //
    public function normas(){
        return response()->json([
            "normasCasa" => NormaCasa::all()
        ]);
    }
}
