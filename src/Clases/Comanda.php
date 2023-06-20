<?php

class Comanda
{
    public $id;
    public $nombre_cliente;
    public $numero_pedido;
    public $id_mesa;
    public $fecha_alta;
    public $puntuacion_mesa;
    public $puntuacion_restaurante;
    public $puntuacion_mozo;
    public $puntuacion_cocinero;
    public $reseña;

    public function __construct($nombre_cliente, $numero_pedido, $id_mesa, $fecha_alta = null,
    $puntuacion_mesa = null, $puntuacion_restaurante = null, $puntuacion_mozo = null,
    $puntuacion_cocinero = null, $reseña = null, $id = null)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->numero_pedido = $numero_pedido;
        $this->id_mesa = $id_mesa;
        if($fecha_alta == null){
            $this->fecha_alta = new DateTime();
        }
        else{
            $this->fecha_alta = $fecha_alta;
        }
        if($puntuacion_mesa != null){
            $this->puntuacion_mesa = $puntuacion_mesa;
        }
        if($puntuacion_restaurante != null){
            $this->puntuacion_restaurante = $puntuacion_restaurante;
        }
        if($puntuacion_mozo != null){
            $this->puntuacion_mozo = $puntuacion_mozo;
        }
        if($puntuacion_cocinero != null){
            $this->puntuacion_cocinero = $puntuacion_cocinero;
        }
        if($reseña != null){
            $this->reseña = $reseña;
        }
        if($id != null){
            $this->id = $id;
        }
    }
    

}
