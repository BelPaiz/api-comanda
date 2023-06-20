<?php
require_once '../src/Clases/Producto.php';
class Pedido
{
    public $id;
    public $numero_pedido;
    public $items;

    public function __construct($items = null, $numero_pedido = null, $id = null)
    {
        $this->items = array();
        if($items != null){
            $this->items = $items; 
        }
        if($numero_pedido == null){
            $this->numero_pedido = rand(1, 99999);
        }
        else{
            $this->numero_pedido = $numero_pedido;
        }
        if($id != null){
            $this->id = $id;
        }
    }
    public function Alta_pedido()
	{
        $itemsJson = json_encode($this->items);
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into pedidos (numero_pedido, items)values('$this->numero_pedido','$itemsJson')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosPedidos()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, numero_pedido as numero_pedido, items as items from pedidos");
        $consulta->execute();
        $arrayObtenido = array();
        $pedidos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $pedido = new Pedido($i->numero_pedido, $i->items, $i->id );
            $pedidos[] = $pedido;
        }
        return $pedidos;
	}
    public static function TraerUnPedido_Id($id) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}
    public static function TraerUnPedido_Numero_pedido($numero_pedido) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos where numero_pedido = ?");
        $consulta->bindValue(1, $numero_pedido, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}
    public function Cargar_item_nuevo($id_producto)
    {
        $producto = Producto::TraerUnProducto_Id($id_producto);
        $producto_pedido = array(
            "nombre"=>$producto->nombre,
            "estado"=>0,
            "tiempo"=>0
        );
        array_push($this->items,$producto_pedido);
    }
    public function Cambiar_estado_item($id_producto, $estado)
    {
        $sector = null;
        $producto = Producto::TraerUnProducto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $i->estado = $estado;
                $sector = $producto->sector;
                return $sector;
            }
        }
        return $sector;
    }
    public function Agregar_tiempo_item($id_producto, $tiempo_minutos)
    {
        $producto = Producto::TraerUnProducto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $i->tiempo = $tiempo_minutos;
                return true;
            }
        }
        return false;
    }
    public function Clcular_tiempo_total_pedido(){
        $tiempo_acumulado = 0;
        foreach($this->items as $i){
            $tiempo_acumulado += $i->tiempo;           
        }
        return $tiempo_acumulado;
    }
    public function Ver_tiempo_restante($id_producto){
        $ahora = new DateTime();
        $minutos = null;
        $producto = Producto::TraerUnProducto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $minutos = $i->tiempo;
                break;
            }
        }
        $tiempoObjetivo = $ahora->modify("+{minutos} minutes");
        $diferencia = $ahora->diff($tiempoObjetivo);
        $minutosRestantes = $diferencia->i;
        return $minutosRestantes;
    }
    public function Actualizar_items_BD()
    {
        $itemsJson = json_encode($this->items);
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update pedidos set items = ? where id = ?");
        $consulta->bindValue(1, $itemsJson, PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function MapearParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                switch($i->estado){
                    case 0:
                        $i->estado = "Pendiente";
                    break;
                    case 1:
                        $i->estado = "En preparacion";
                    break;
                    case 2:
                        $i->estado = "Listo para servir";
                    break;
                }
            }
        }
        return $array;
    }
}


