<?php
require_once 'Controller/GalleryController.php';
require_once 'RouterClass.php';

// CONSTANTES PARA RUTEO
define("BASE_URL", 'http://' . $_SERVER["SERVER_NAME"] . ':' . $_SERVER["SERVER_PORT"] . dirname($_SERVER["PHP_SELF"]) . '/');
//esto lo agrego para comparar
$r = new Router();

// rutas
$r->addRoute("login ", "GET", "GalleryController", "Login");
// $r->addRoute("login", "POST", "GalleryController", "LoginChecker");
$r->addRoute("login", "POST", "GalleryController", "verifyUser");

$r->addRoute("logout", "GET", "GalleryController", "Logout");

$r->addRoute("main", "GET", "GalleryController", "Main");

$r->addRoute("create", "GET", "GalleryController", "createCard");

$r->addRoute("list", "GET", "GalleryController", "CreateList");

$r->addRoute("admin", "GET", "GalleryController", "Admin");
$r->addRoute("admin-login", "POST", "GalleryController", "verifyAdmin");

//parte del registro y login
// $r->addRoute("register", "POST", "LoginController", "Register");
// $r->addRoute("loginscreen", "GET", "LoginController", "Login");
// $r->addRoute("login", "POST", "LoginController", "verifyUser");


//Ruta por defecto.
$r->setDefaultRoute("GalleryController", "Login");

//run
$r->route($_GET['action'], $_SERVER['REQUEST_METHOD']);
