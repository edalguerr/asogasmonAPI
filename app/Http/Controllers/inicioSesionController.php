<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Usuario;

class inicioSesionController extends Controller
{
    /**
     * verifica los datos de inicio de sesion
     *
     */
    public function signin()
    {
        //
        $usuarios = Usuario::where('email','eguerreroa2@gmail.com')->get();
        
        echo '<ul>';

        foreach ($usuarios as  $value) {
            # code...
            echo "<li>" . $value->NOMBRE . "</li> ";
        }
        
        echo '</ul>';
       
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
        //
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
