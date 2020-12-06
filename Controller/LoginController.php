<?php

require_once "./Model/UserModel.php";
require_once "./View/GalleryView.php";

class LoginController
{
    private $UserModel;
    private $view;

    function __construct()
    {
        $this->UserModel = new UserModel();
        $this->view = new GalleryView();
    }

    function Register()
    {

        if ((isset($_POST["username"])) && (isset($_POST["password"]))) {

            $username = $_POST["username"];
            $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

            $this->UserModel->RegisterUser($username, $hash);

            //traigo el usuario que acabo de mandar para traer la id
            $user = $this->UserModel->getByUsername($username);

            //arranco la sesion
            $this->startSession($user->id, $user->nombre, $user->admin_auth);

            $this->view->ShowHomeLocation();
        }
    }

    function Logout()
    {
        session_start();
        session_destroy();
        $this->view->ShowHomeLocation();
    }

    function Login()
    {
        $this->view->ShowLogin();
    }

    function GetSessionAuthLevel()
    //comprueba si esta logueado para cambiar el aside, basado en el nivel de autorizacion
    // hecho para no repetir las llamadas a session_start y lanzar errores.
    {
        if (isset($_SESSION["AUTH"])) {
            //si retorna algo, es porque antes se hizo un session_start
            return $_SESSION["AUTH"];
        } else {

            //si no está seteado, puede ser porque antes no se hizo un session_start.
            session_start();
            if (isset($_SESSION["AUTH"])) {
                return $_SESSION["AUTH"];
            }
            //y si no está seteado, entonces nunca existió en un principio y devuelve null.
            else return null;
        }
    }


}
