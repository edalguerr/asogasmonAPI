<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Categoria;

class CategoriaController extends Controller
{
    //
    public function index()
    {

        $categorias = Categoria::all();
        
        //obteniendo las subCategorias de cada categoria
        foreach ($categorias as $categoria) {        
            $categoria->subCategorias;
        }

        return response()->json([
            'categorias' => $categorias
        ]);
    }
}
