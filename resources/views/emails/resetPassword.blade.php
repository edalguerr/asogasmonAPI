<p>Hola, {{$nombreDestinatario}}.</p>
<p>
    Hemos recibido una solicitud para modificar tu contraseña; para hacerlo accede al
    siguiente enlace:
</p>

<p>
    <!--<a href="{{ url("api/logon/{$token}") }}">Modificar contraseña</a>-->
    <a href="localhost/reset/{{$email}}/resetPass/{{$token}}">Modificar contraseña</a>
</p>

<p>
    Si no has realizado la solicitud ignora este mensaje.
</p>
