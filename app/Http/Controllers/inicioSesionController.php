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
                    'usuario' => null
                ]);
            }
        } //inicando sesion sin token, no habia sesion activa
        else {

            //buscando usuario por su email y contrasenia
            $usuarios = Usuario::where('email', $request->email)->where('contrasenia', $request->contrasenia)->first();

            //si los datos  no coinciden con ningun usuario
            if (count($usuarios) < 1) {

                return response()->json([
                    'usuario' => null
                ]);
            }
        }


        //usuario encontrado, inicio de sesion concedido
        //generamos y actualizamos el token
        $token = Str::random(60);
        $usuarios->ID_TOKEN = md5($token);

        Usuario::where('ID', $usuarios->ID)->update([
            'ID_TOKEN' => $usuarios->ID_TOKEN
        ]);

        //print_r($userActual);

        return response()->json([
            'usuario'  => $usuarios
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
        $usuarios = Usuario::where('email', $request->email)->first();

        //si el correo ingresado ya esta en uso
        if (count($usuarios) >= 1) {
            return response()->json([
                'usuario'  => null,
                'mensaje' => 'Email en uso, intente con otro'
            ]);
        }

        $usuario = Usuario::create([
            'NOMBRE' => $request->input('nombre'),
            'APELLIDOS' => $request->input('apellidos'),
            'EMAIL' => $request->input('email'),
            'CONTRASENIA' => $request->input('contrasenia'),
            'FOTO' => ($request->has('foto') ? $request->input('foto') : 'avatar.png'),
            'ID_TOKEN' => md5(Str::random(60))
        ]);

        return response()->json([
            'usuario'  => $usuario,
            'mensaje' => 'Bienvenido'
        ]);
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
        //POR HACER: verificar que campos se van a actualizar                
        Usuario::where('ID', $id)->update([
            'NOMBRE' => $request->input('nombre'),
            'APELLIDOS' => $request->input('apellidos'),
            'EMAIL' => $request->input('email'),
            'CONTRASENIA' => md5($request->input('contrasenia')),
        ]);

        return response()->json([
            'usuario'  => (Usuario::where('ID', $id)->first()),
            'request' => $request
        ]);
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
