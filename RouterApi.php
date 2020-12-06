<?php
require_once 'RouterClass.php';
require_once 'api/ApiBingoController.php';

// instacio el router
$router = new Router();

// armo la tabla de ruteo de la API REST
// $router->addRoute('obras', 'GET', 'ApiCommentController', 'Prueba');
$router->addRoute('ronda', 'GET', 'ApiBingoController', 'GetSessionState');
$router->addRoute('carton', 'GET', 'ApiBingoController', 'GetBingoCards');
$router->addRoute('numeros', 'GET', 'ApiBingoController', 'GetNumbers');
$router->addRoute('usuario', 'GET', 'ApiBingoController', 'GetUserData');

$router->addRoute('mark/carton/:ID', 'POST', 'ApiBingoController', 'MarkOwnerBingoCard');

$router->addRoute('mark/line', 'POST', 'ApiBingoController', 'MarkLineWinner');
$router->addRoute('mark/bingo', 'POST', 'ApiBingoController', 'MarkBingoWinner');



$router->addRoute('ganador-linea', 'GET', 'ApiBingoController', 'GetLineWinner');
$router->addRoute('ganador-bingo', 'GET', 'ApiBingoController', 'GetBingoWinner');


$router->addRoute('obras/:ID', 'GET', 'ApiBingoController', 'GetCommentsByArtworkId');
$router->addRoute('obras/:ID', 'POST', 'ApiBingoController', 'InsertComment');
$router->addRoute('comentarios/:ID', 'DELETE', 'ApiBingoController', 'DeleteComment');

// $router->addRoute('obras', 'POST', 'ApiCommentController', 'InsertTask');


// $router->addRoute('obras/:ID', 'PUT', 'ApiCommentController', 'UpdateTask');


 //run
 $router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']); 