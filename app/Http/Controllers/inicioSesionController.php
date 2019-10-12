<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Usuario;

class inicioSesionController extends Controller
{


    public function index(){

        $usuarios = Usuario::all();

        return response( $usuarios);
    }

    /**
     * verifica los datos de inicio de sesion
     *
     */
    public function signin(Request $request)
    {
        //
        //print_r($request->all());
        $usuarios = Usuario::where('email',$request->email)->where('contrasenia',$request->contrasenia)->first();
        
        if(count($usuarios) < 1){
            
            return response()->json([
                'usuario'=> null
            ]);
        }
        
        /*
        echo '<ul>';

        foreach ($usuarios as  $value) {
            # code...
            echo "<li>" . $value->NOMBRE . "</li> ";
        }
        
        echo '</ul>';*/
        
        return response()->json([
            'usuario'=> $usuarios
        ]);
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //por hacer: verificar los datos recibidos y su formato
        $usuario = Usuario::create([
            'nombre' => $request->input('nombre'),
            'apellidos' => $request->input('apellidos'),
            'email' => $request->input('email'),
            'contrasenia' => md5($request->input('contrasenia')),
            'foto' => ($request->has('foto')?$request->input('foto'):'avatar.png')
        ]);

        return $request;
        
    }

    
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //0
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}