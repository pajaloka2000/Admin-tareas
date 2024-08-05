<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController{
    public static function index(Router $router){

        //Sesion iniciada
        session_start();

        //Proteger la ruta si no se ha iniciado sesion
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        //Renderizar la vista
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        
        //Sesion iniciada
        session_start();

        //Proteger la ruta si no se ha iniciado sesion
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);

            //Validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //Generar una url unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el proyecto
                $proyecto->guardar();

                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        //Renderizar la vista
        $router->render('dashboard/crear-proyecto',[
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
        ]);
    }

    public static function proyecto(Router $router){
        //Sesion iniciada
        session_start();

        //Proteger la ruta si no se ha iniciado sesion
        isAuth();

        $token = $_GET['id'];

        if(!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto es quien lo creó
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto',[
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router){
        //Sesion iniciada
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){

                $exitesUsuario = Usuario::where('email', $usuario->email);
                
                if($exitesUsuario && $exitesUsuario->id !== $usuario->id ){
                    //Mensaje de error
                    Usuario::setAlerta('error', 'Email no valido, ya pertenece a otra cuenta');
                    $alertas = $usuario->getAlertas();
                }else{
                    //Guardar el registro
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado correctamente');
                    $alertas = $usuario->getAlertas();

                    //Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }


        //Renderizar la vista
        $router->render('dashboard/perfil',[
            'titulo' => 'Perfil',
            'usuario' => $usuario, 
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){

        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            //Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();

                if ($resultado) {
                    $usuario->password = $usuario->password_nuevo;

                    //Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    //Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar
                    $resultado = $usuario->guardar();

                    //Asignar el nuevo password
                    if($resultado){
                        Usuario::setAlerta('exito', 'Password guardado correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                }else{
                    Usuario::setAlerta('error', 'Password incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password',[
            'titulo' => 'Cambiar password',
            'alertas' => $alertas
        ]);
    }

}


?>