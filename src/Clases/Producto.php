<?php
class Producto
{
    public $id;
    public $nombre;
    public $sector;

    public function __construct($nombre, $sector, $id = null)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
        if($id != null){
            $this->id = $id;
        }
    }
    public function InsertarProducto()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre, sector)values('$this->nombre','$this->sector')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosProductos()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id_producto as id, nombre as nombre, sector as sector from productos");
        $consulta->execute();
        $arrayObtenido = array();
        $productos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Producto($i->nombre, $i->sector, $i->id );
            $productos[] = $producto;
        }
        return $productos;
	}
}
?>