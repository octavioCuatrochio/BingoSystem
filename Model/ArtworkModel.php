<?php

class ArtworkModel
{

    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db_bingo;charset=utf8', 'root', '');

        //al desactivar las preparaciones emuladas, nos permite poner parametros al LIMIT a través de PDO.
        //se puede ver esto en las funciones: getBlockOfArtworks y GetFrontArtworks
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    function GetArtworks()
    {
        $sentencia = $this->db->prepare("SELECT * FROM obra");
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function GetArtwork($id_piece)
    {
        $sentencia = $this->db->prepare("SELECT * FROM obra WHERE id=?");
        $sentencia->execute(array($id_piece));
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function GetArtworksByCategory($id_category)
    {
        $sentencia = $this->db->prepare("SELECT * FROM obra WHERE id_categoria=?");
        $sentencia->execute([$id_category]);
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function AddArtwork($nombre, $descripcion, $autor, $anio, $imagen, $category)
    {
        //si está seteada, quiere decir que hay una imagen en la carpeta temporal de php.
        //si no lo está, entonces estoy colocando una foto por default y no me importa el "fileTemp", solo el "filePath".
        if (isset($imagen["fileTemp"])) {
            move_uploaded_file($imagen["fileTemp"], $imagen["filePath"]);
        }

        $sentencia = $this->db->prepare("INSERT INTO obra(nombre, descripcion, autor, anio,imagen,id_categoria) VALUES(?,?,?,?,?,?)");
        $sentencia->execute(array($nombre, $descripcion, $autor, $anio, $imagen["filePath"], $category));
    }

    function GetFrontArtworks($limit)
    {
        $sentencia = $this->db->prepare("SELECT * FROM obra LIMIT ?");
        $sentencia->execute(array($limit));
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function DeleteArtwork($art_id)
    {
        $sentencia = $this->db->prepare("DELETE FROM obra WHERE id=?");
        $sentencia->execute([$art_id]);
    }


    function UpdateArtwork($nombre, $descripcion, $autor, $anio, $imagen, $category, $art_id)
    {

        if (isset($imagen["fileTemp"])) {
            move_uploaded_file($imagen["fileTemp"], $imagen["filePath"]);
        }

        $sentencia = $this->db->prepare("UPDATE obra SET nombre=?, descripcion=?, autor=?, anio=?, imagen=?, id_categoria=? WHERE id=?");
        $sentencia->execute(array($nombre, $descripcion, $autor, $anio, $imagen["filePath"], $category, $art_id));
    }

    function GetArtworkAndCategories()
    {
        $sentencia = $this->db->prepare("SELECT obra.*, categoria.nombre_category FROM obra JOIN categoria ON obra.id_categoria = categoria.id");
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function GetRowCount()
    {
        $sentencia = $this->db->prepare("SELECT COUNT(*) FROM obra");
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    function GetArtworkAndCategoryById($obra_id)
    {
        $sentencia = $this->db->prepare("SELECT obra.*, categoria.nombre_category FROM obra JOIN categoria ON obra.id_categoria = categoria.id WHERE obra.id=?");
        $sentencia->execute([$obra_id]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getBlockOfArtworks($offset, $quantity)
    {
        $sentencia = $this->db->prepare("SELECT obra.id, obra.nombre, obra.descripcion, obra.autor, obra.imagen FROM obra ORDER BY id LIMIT ?,?");
        $sentencia->execute(array($offset, $quantity));
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }
}
