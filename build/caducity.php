<?php

    /**
     * Desarrollo web en Entorno Servidor
     * curso 2024|25
     * 
     * @author Antonio J. Sánchez    
     */

    session_start() ;    
    
    if ((empty($_SESSION)) || 
        (time() >= $_SESSION["time"])):            
            $_SESSION = [] ;
            die(header("location: login.php")) ;    # mejor redirigir al login
    endif ;

    
    # actualizamos el tiempo de sesion
    $_SESSION["time"] = time() + 300;
    
    # definimos el token (evita ataques csrf)
    $_SESSION["token"] = md5(time()) ;

?>