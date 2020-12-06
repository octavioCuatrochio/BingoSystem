<?php

class UserModel
{

    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=db_bingo;charset=utf8', 'root', '');
    }

    function getByUsername($username)
    {
        $sentencia = $this->db->prepare("SELECT * FROM usuario WHERE nombre=?");
        $sentencia->execute([$username]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getAllUsers()
    {
        $sentencia = $this->db->prepare("SELECT * FROM usuario");
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }

    function getPermissionsById($user_id)
    {
        $sentencia = $this->db->prepare("SELECT usuario.admin_auth FROM usuario WHERE id=?");
        $sentencia->execute([$user_id]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getAllById($user_id)
    {
        $sentencia = $this->db->prepare("SELECT * FROM usuario WHERE id=?");
        $sentencia->execute([$user_id]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getInfoById($user_id)
    {
        $sentencia = $this->db->prepare("SELECT usuario.id, usuario.nombre, usuario.admin_auth FROM usuario WHERE id=?");
        $sentencia->execute([$user_id]);
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function UpdateUser($user_id, $nombre, $auth)
    {
        $sentencia = $this->db->prepare("UPDATE usuario SET nombre=?, admin_auth=? WHERE id=?");
        $sentencia->execute(array($nombre, $auth, $user_id));
    }

    function DeleteUser($user_id)
    {
        $sentencia = $this->db->prepare("DELETE FROM usuario WHERE id=?");
        $sentencia->execute([$user_id]);
        return $sentencia->rowCount();
    }

    function RegisterUser($username, $hash)
    {
        $sentencia = $this->db->prepare("INSERT INTO usuario(nombre,password) VALUES(?,?)");
        $sentencia->execute(array($username, $hash));
    }
}
