<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $password_actual;
    public $password_nuevo;
    public $token;
    public $confirmado;


    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;

    }

    //Validar el login de usuarios
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El email no válido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password del usuario no puede ir vacio';
        }
        return self::$alertas;
    }

    //Validacion para nuevas cuentas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password del usuario no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        if($this->password != $this->password2){
            self::$alertas['error'][] = 'Los passwords son diferentes';
        }
        return self::$alertas;
    }

    //Valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El email no válido';
        }

        return self::$alertas;
    }

    //Valida el password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El password del usuario no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    //Comprobar password
    public function comprobar_password() : bool{
        return password_verify($this->password_actual, $this->password);
    }

    //Hashear el password
    public function hashPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar un token
    public function crearToken() : void{
        $this->token = uniqid();
    }
    
    //Validar perfil del usuario en el dashboard
    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        return self::$alertas;
    }

    //Validasion para cambiar el password
    public function nuevo_password() : array{
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'El password actual no puede ir vacio';
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'El password nuevo no puede ir vacio';
        }
        if (strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El password nuevo debe contener 6 caracteres';
        }
        return self::$alertas;
    }
}

?>