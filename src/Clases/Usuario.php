<?php
class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $subTipo;
    public $sector;
    public $email;
    public $password;
    public $token;
    public $token_exp;
    public $fechaRegistro;

    public function __construct($nombre, $apellido, $tipo, $email, $password = null, $subTipo = null, $sector = null, $fechaRegistro = null, $id = null, $token = null, $token_exp = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
        $this->email = $email;
        if($id != null){
            $this->id = $id;
        }
        if($subTipo != null){
            $this->subTipo = $subTipo;
        }
        if($sector != null){
            $this->sector = $sector;
        }
        if($fechaRegistro == null){
            $this->fechaRegistro =  date("Y-m-d");
        }
        else{
            $this->fechaRegistro = $fechaRegistro;
        }
        if($password != null){
            $this->password = $password;
        }
        else{
            $this->password = '12345';
        }
        if($token != null){
            $this->token = $token;
        }
        if($token_exp != null){
            $this->token_exp = $token_exp;
        }
    }
    public function InsertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into usuarios (nombre,apellido,tipo, sub_tipo, sector, email, contraseña, token, token_exp, fecha_registro)values('$this->nombre','$this->apellido','$this->tipo', '$this->subTipo', '$this->sector', '$this->email', '$this->password', '$this->token', '$this->token_exp', '$this->fechaRegistro')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosUsuarios()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, contraseña as password, token as token, token_exp as token_exp, fecha_registro as fechaRegistro from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->nombre, $i->apellido, $i->tipo, $i->email, $i->password, $i->subTipo, $i->sector,$i->fechaRegistro, $i->id , $i->token, $i->token_exp);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}
    public static function TraerUnUsuarioId($id) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from usuarios where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token, $usuarioBuscado->token_exp);
        }
        return $usuario;
	}
    public static function TraerUnUsuarioEmail($email) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from usuarios where email = ?");
        $consulta->bindValue(1, $email, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token, $usuarioBuscado->token_exp);
        }
        return $usuario;
	}
    public static function TraerUnUsuario_Token($token) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from usuarios where token = ?");
        $consulta->bindValue(1, $token, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token, $usuarioBuscado->token_exp);
        }
        return $usuario;
	}
    public static function Definir_token($id, $email){
        $time = time();
        $token = array(
         
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60*60*24), //Tiempo de expiracion del token (1 dia)
            "data" => [
                "id" => $id,
                "email" => $email
            ]
        );
        return $token;
    }
    public function ModificarTokenDB($data){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update usuarios set token = ?, token_exp = ? where id = ?");
        $consulta->bindValue(1, $data["token"], PDO::PARAM_STR);
        $consulta->bindValue(2, $data["token_exp"], PDO::PARAM_STR);
        $consulta->bindValue(3, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function ValidarToken($token, $tipo, $subTipo = null){
        $usuario = null;
        $time = time();
        $usuario = self::TraerUnUsuario_Token($token);
        $resp = "No autorizado";
        if($usuario != null && $usuario->tipo == $tipo){
            if($time < $usuario->token_exp){
               if($subTipo != null){
                    if($usuario->subTipo == $subTipo){
                        $resp =  "Validado";
                    }
                }
                else{
                    $resp =  "Validado";
                }
            }
            else{
                $resp = "Sesion expirada";
            }
        }
        return $resp;
    }
    public static function FiltrarParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i->password);
                unset($i->token);
                unset($i->token_exp);
            }
            return $array;
        }
    }
}
?>