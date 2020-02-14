<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/**********************************************************
 ***************** INICIO DE SESION ***********************
 * ********************************************************
 */
Route::resource('login', 'inicioSesionController')->except(
    [
        'index', 'create', 'edit', 'show', 'destroy'
    ]
);

Route::post('/signin', 'inicioSesionController@signin');
Route::post('/signin/update', 'inicioSesionController@updatePassword');
Route::post('/signin/avatar', 'inicioSesionController@avatar');
Route::post('/signin/updateAvatar', 'inicioSesionController@updateAvatar');
Route::post('/signin/resetPass', 'inicioSesionController@resetPassword');

/**********************************************************
 *****************SERVICIOS ESPECIFICOS********************
 **********************************************************
 */
Route::post('serviciosEspecificos', 'ServicioEspecificoController@servicios');

/**********************************************************
 *****************NORMAS DE LA CASA************************
 **********************************************************
 */
Route::post('normasCasa', 'NormaCasaController@normas');


/**********************************************************
 ***************** OFERTA CASA_APTO ***********************
 **********************************************************
 */

Route::resource('ofertaCasaApto', 'OfertaCasaAptoController')->except(
    [
        'create', 'edit', 'update', 'destroy', 'show'
    ]
);

Route::post('ofertaCasaApto/apartamentos', 'OfertaCasaAptoController@obtenerOfertas');

/**********************************************************
 ***************** OFERTA HABITACION **********************
 **********************************************************
 */

Route::resource('ofertaHabitacion', 'OfertaHabitacionController')->except(
    [
        'create', 'edit', 'update', 'destroy', 'show'
    ]
);

Route::post('ofertaHabitacion/habitaciones', 'OfertaHabitacionController@obtenerOfertas');

/**********************************************************
 ***************** OFERTA PENSION **********************
 **********************************************************
 */

Route::resource('ofertaPension', 'OfertaPensionController')->except(
    [
        'create', 'edit', 'update', 'destroy', 'show'
    ]
);

Route::post('ofertaPension/pensiones', 'OfertaPensionController@obtenerOfertas');
