<?php
class Mesa
{
    public $id;
    public $estado;

    public function __construct($estado, $id = null)
    {
        $this->estado = $estado;
        if($id != null){
            $this->id = $id;
        }
    }
    public function InsertarMesa()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into mesas (estado)values('$this->estado')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLasMesas()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, estado as estado from mesas");
        $consulta->execute();
        $arrayObtenido = array();
        $mesas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $mesa = new Mesa($i->estado, $i->id );
            $mesas[] = $mesa;
        }
        return $mesas;
	}
    public function CambiarEstadoMesa($estado){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update mesas set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
}
?>