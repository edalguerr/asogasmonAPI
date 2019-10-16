<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Usuario;
use App\models\ResetPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Mail\PasswordReset;

class inicioSesionController extends Controller
{


    public function index()
    {
        /*
        $usuarios = Usuario::all();

        return response($usuarios);
        Mail::send('emails.resetPassword',['nombre' => 'ASOGASMON'], function(Message $message){
            $message->to('eguerreroa2@gmail.com','Edwin Alberto')
                    ->from('asogasmontest@gmail.com', 'ASOGASMON')
                    ->subject('Informacion de tu cuenta');

        });
        $nombre = "Edwin alberto";
        Mail::to('eguerreroa2@gmail.com')->send(new PasswordReset($nombre));*/

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
        $usuarios = Usuario::where('email', $request->email)->first();

        //si el correo ingresado ya esta en uso
        if (count($usuarios) >= 1) {
            return response()->json([
                'usuario'  => null,
                'mensaje' => 'Email en uso, intente con otro'
            ]);
        }
        
        Usuario::where('ID', $id)->update([
            'NOMBRE' => $request->input('nombre'),
            'APELLIDOS' => $request->input('apellidos'),
            'EMAIL' => $request->input('email'),
            'ID_TOKEN' => md5(Str::random(60))
        ]);

        return response()->json([
            'usuario'  => (Usuario::where('ID', $id)->first())
        ]);
    }


    /**
     * 1)Crea el token de enlace que se enviará como parte de la direccion
     * para reestablecer la contraseña
     * 2)Enviar email con direccion de enlace para resetear la contraseña     
     */
    public function resetPassword(Request $request){

        $usuario = Usuario::where('email', $request->email)->first();

       
        //si no se encontró el usuario
        if(count($usuario) < 1){
            return response()->json([
                'usuario' => null
            ]);
        }

        
        //generamos el token de enlace y actualizamos o creamos el registro para la 
        //tabla reset password 
        $token_enlace = md5(Str::random(60));        
        $resets = ResetPassword::where('email', $request->email)->first();

        //si no hay enlaces generados, crea uno
        if(count($resets) < 1){
            
            ResetPassword::create([
                'EMAIL' =>  $request->email,
                'TOKEN_ENLACE' => $token_enlace 
            ]);

        }
        //si hay enlaces generados, lo actualiza
        else{

            ResetPassword::where('email',  $request->email)->update([
                'EMAIL' =>  $request->email,
                'TOKEN_ENLACE' => $token_enlace            
            ]);
        }
     
        
        //enviamos el email 
        $nombre = $usuario->NOMBRE;
        Mail::to($request->email)->send(new PasswordReset($nombre, $token_enlace));

        return response()->json([
            'usuario' => "Email enviado"
        ]);
    }


    /**
     * Actualizar contraseña de usuario
     */
    public function updatePassword(Request $request){
        //actualiza la contraseña de usuario
        //usando el token y la tabla reset_password

        $usuario = Usuario::where('email', $request->email)->first();
        
        //si no se encontró el usuario
        if(count($usuario) < 1){
            return response()->json([
                'usuario' => null
            ]);
        }

        //buscamos el token en la tabla reset_password
        $resets = ResetPassword::where('email', $request->email)->where('token_enlace',$request->token)->first();

        //si no se encuentra el token de enlace
        if(count($resets) < 1){
            return response()->json([
                'usuario' => null
            ]);
        }       
        
        //ACTUALIZA LA CONTRASEÑA DE USUARIO
        Usuario::where('email', $request->email)->update([
            'CONTRASENIA' => $request->input('contrasenia')
        ]);

        //ELIMINAMOS EL TOKEN DE ENLACE DE LA TABLA reset_password
        ResetPassword::where('email', $request->email)->where('token_enlace',$request->token)->delete();

        return response()->json([
            'usuario'  => "Contraseña de usuario actualizada"
        ]);
    }


    public function indexToken(Request $request, $token){
        echo "exito<br>";
        echo 'Correcto: ' . $token;
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
