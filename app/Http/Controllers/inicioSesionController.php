<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Usuario;
use Illuminate\Support\Str;


class inicioSesionController extends Controller
{


    public function index()
    {

        $usuarios = Usuario::all();

        return response($usuarios);
    }

    /**
     * POR HACER:
     * verifica los datos de inicio de sesion
     *
     */
    public function signin(Request $request)
    {
        //
        //print_r($request->all());

        
        //sin el token es diferente de null, hay una sesion activa
        if ($request->id_token != null && $request->id_token != "null") {

            //buscando usuario por su token activo
            $usuarios = Usuario::where('id_token', $request->id_token)->first();

            //si no econtró ningún token valido
            if (count($usuarios) < 1) {

                return response()->json([
                    'usuario' => null,
                    'busqueda' => 'por token fallida',
                    'token' => $request->id_token 
                ]);
            }

        } //inicando sesion sin token, no habia sesion activa
        else{
            
            //buscando usuario por su email y contrasenia
            $usuarios = Usuario::where('email', $request->email)->where('contrasenia', $request->contrasenia)->first();

            
            //si los datos  no coinciden con ningunb usuario
            if (count($usuarios) < 1) {

                return response()->json([
                    'usuario' => null,
                    'busqueda' => 'por email fallida'
                ]);
            }
            
        }
            
        
        //usuario encontrado, inicio de sesion concedido
        //generamos y actualizamos el token
        $token = Str::random(60);

        //print_r($usuarios);

        foreach ($usuarios as $user) {
            # code...
            echo 'nombre: ' . $user->NOMBRE;
            //$user->id_token = md5($token);
        }
        

        return response()->json([
            'usuario' => $usuarios,
            'busqueda' => 'exitosa'
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
        //POR HACER: verificar los datos recibidos y su formato
        $usuario = Usuario::create([
            'nombre' => $request->input('nombre'),
            'apellidos' => $request->input('apellidos'),
            'email' => $request->input('email'),
            'contrasenia' => md5($request->input('contrasenia')),
            'foto' => ($request->has('foto') ? $request->input('foto') : 'avatar.png')
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
