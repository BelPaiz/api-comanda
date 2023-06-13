<?php
class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $subTipo;
    public $sector;
    public $fechaRegistro;

    public function __construct($nombre, $apellido, $tipo, $subTipo = null, $sector = null, $fechaRegistro = null, $id = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
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
    }
    public function InsertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into usuarios (nombre,apellido,tipo, sub_tipo, sector, fecha_registro)values('$this->nombre','$this->apellido','$this->tipo', '$this->subTipo', '$this->sector', '$this->fechaRegistro')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosUsuarios()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, fecha_registro as fechaRegistro from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->nombre, $i->apellido, $i->tipo, $i->subTipo, $i->sector,$i->fechaRegistro, $i->id );
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
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->subTipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id );
        }
        return $usuario;
	}
}
?>