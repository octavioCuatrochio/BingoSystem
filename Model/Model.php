<?php

class Model
{

    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db_bingo;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    function GetEstadoDeJuego()
    {
        $sentencia = $this->db->prepare("SELECT juego.estado FROM juego");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getByDNI($dni)
    {
        $sentencia = $this->db->prepare("SELECT * FROM usuario WHERE dni = ?");
        $sentencia->execute([$dni]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function setEstadoByDNI($estado, $dni)
    {
        $sentencia = $this->db->prepare("UPDATE usuario SET estado=? WHERE dni=?");
        $sentencia->execute(array($estado, $dni));
    }

    function GetBingoCards()
    {
        $sentencia = $this->db->prepare("SELECT * FROM carton LIMIT 6");
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function InserBingoCard($lista_id, $matrix)
    {
        $sentencia = $this->db->prepare("INSERT INTO carton(lista_id, numeros) VALUES(?,?)");
        $sentencia->execute(array($lista_id, $matrix));
        return $sentencia->rowCount();
    }

    function MarkCardAsUsed($card_id)
    {
        $sentencia = $this->db->prepare("UPDATE carton SET usado = 1 WHERE id=?");
        $sentencia->execute(array($card_id));
    }

    function addOwnerToCard($card_id, $dni)
    {
        $sentencia = $this->db->prepare("UPDATE carton SET dni_jugador = ? WHERE id=?");
        $sentencia->execute(array($dni, $card_id));
        return $sentencia->rowCount();
    }

    function getNumbers()
    {
        $sentencia = $this->db->prepare("SELECT juego.numeros FROM juego");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function InsertEmptyList()
    {
        //la arranco vacia y con solo la devolucion del id
        $sentencia = $this->db->prepare("INSERT INTO lista VALUES (NULL, NULL)");
        $sentencia->execute();
        return $this->db->lastInsertId();
    }

    function getAdminByNombre($nombre)
    {
        $sentencia = $this->db->prepare("SELECT * FROM admin WHERE nombre = ?");
        $sentencia->execute(array($nombre));
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function GetLineWinner()
    {
        $sentencia = $this->db->prepare("SELECT juego.ganador_linea FROM juego");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function GetBingoWinner()
    {
        $sentencia = $this->db->prepare("SELECT juego.ganador_carton FROM juego");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function addLineWinner($dni)
    {
        $sentencia = $this->db->prepare("UPDATE juego SET ganador_linea = ?");
        $sentencia->execute(array($dni));
        return $sentencia->rowCount();
    }

    function addBingoWinner($dni)
    {
        $sentencia = $this->db->prepare("UPDATE juego SET ganador_carton = ?");
        $sentencia->execute(array($dni));
        return $sentencia->rowCount();
    }

    function getAdmin()
    {
        $sentencia = $this->db->prepare("SELECT * FROM admin");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function ModifyAdmin($id, $hash)
    {
        $sentencia = $this->db->prepare("UPDATE admin SET clave = ? WHERE id=?");
        $sentencia->execute(array($hash, $id));
    }
}
