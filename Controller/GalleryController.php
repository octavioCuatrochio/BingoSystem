<?php

require_once "./Model/Model.php";
require_once "./View/View.php";
require_once "./CardAlgorithm.php";
require_once './dompdf/autoload.inc.php';

class GalleryController
{
    private $model;
    private $view;




    function __construct()
    {
        $this->model = new Model();
        $this->view = new View();
        date_default_timezone_set('America/Buenos_Aires');
    }

    // Anotaciones que serviran para entender el codigo en forma global:

    //cada vez que se llama a la funcion "requestSessionInfo()" se pide a la $_SESSION el nombre y la autorizacion
    // para mostrar el nombre y el aside acorde a cada usuario(no registrado, usuario, admin) en la vista;

    // cada vez que se llama a la funcion "checkPermissions()" comprueba desde la db si el usuario tiene permisos para
    // entrar a determinada pagina;

    function Login()
    {
        $this->view->ShowLoginScreen();
    }

    function Main()
    {
        $this->checkPermissions();
        $this->requestSessionInfo();

        // $juego = $this->model->GetEstadoDeJuego();
        $this->view->ShowMain();
    }

    function Admin()
    {
        // $this->encryptAdmin();
        $this->view->ShowAdminLogin();
    }

    function encryptAdmin()
    {
        $admin = $this->model->getAdmin();

        $hash = password_hash($admin->clave, PASSWORD_DEFAULT);

        $this->model->ModifyAdmin($admin->id, $hash);
    }

    function verifyUser()
    {
        //si no está seteado alguno de los 2 POST, entonces no lleno todos los campos
        if ((isset($_POST["dni"])) && (isset($_POST["phone_number"]))) {

            $dni = $_POST["dni"];
            $phone = $_POST["phone_number"];

            $user = $this->model->getByDNI($dni);

            //si existe ese usuario y la contraseña es la misma, entonces...
            if (($user != false) && ($user->telefono == $phone)) {

                //arranco la sesion
                $this->startSession($user->dni, $user->nombre, $user->telefono);
                // $this->model->setEstadoByDNI(true, $user->dni);

                $this->view->ShowMainLocation();
            } else $this->view->ShowLogin("Error: D.N.I o Teléfono incorrectos");
        } else $this->view->ShowLogin("Error: Complete todos los campos");
    }


    function startAdminSession($id, $nombre)
    {
        session_start();
        $_SESSION["ID_USER"] = $id;
        $_SESSION["USERNAME"] = $nombre;
    }

    function verifyAdmin()
    {
        //si no está seteado alguno de los 2 POST, entonces no lleno todos los campos
        if ((isset($_POST["name"])) && (isset($_POST["password"]))) {

            $name = $_POST["name"];
            $password = $_POST["password"];

            $user = $this->model->getAdminByNombre($name);

            //si existe ese usuario y la contraseña es la misma, entonces...
            if (($user != false) && (password_verify($password, $user->clave))) {

                //arranco la sesion
                $this->startAdminSession($user->id, $user->nombre);
                $this->requestSessionInfo();

                $this->view->ShowAdminABM();
            } else $this->view->ShowAdminLogin("Error: D.N.I o Teléfono incorrectos");
        } else $this->view->ShowAdminLogin("Error: Complete todos los campos");
    }

    private function startSession($id, $nombre, $telefono)
    {
        session_start();
        $_SESSION["DNI_USER"] = $id;
        $_SESSION["USERNAME"] = $nombre;
        $_SESSION["PHONE"] = $telefono;
    }

    function checkPermissions()
    {
        session_start();
        if (!isset($_SESSION["DNI_USER"])) {
            //si no está seteado, entonces no está registrado
            $this->view->ShowLoginLocation();
            die();
        }
    }

    function checkAdminPermissions()
    {
        session_start();
        if (!isset($_SESSION["ID_USER"])) {
            //si no está seteado, entonces no está registrado
            $this->view->ShowLoginLocation();
            die();
        }
    }

    function requestSessionInfo()
    {
        //asigna a la vista el nombre y la autorizacion del usuario en sesion actualmente
        //para pasarselo al aside;

        $sessionName = $this->GetSessionUsername();
        $this->view->setSessionName($sessionName);
    }


    function GetSessionUsername()
    {
        if (isset($_SESSION["USERNAME"])) {
            return $_SESSION["USERNAME"];
        } else {
            session_start();
            if (isset($_SESSION["USERNAME"])) {
                return $_SESSION["USERNAME"];
            } else {
                return null;
            }
        }
    }

    function createCard($id)
    {
        //altera el 6 por cualquier numero que quieras para crear esa cantidad de cartones
        for ($i = 0; $i < 6; $i++) {
            $algorithm = new CardAlgorithm();
            $matrix = $algorithm->createCard();

            $flag = true;
            //ERRORRRRRRR: se crean columnas de numeros que se pasan el checkeo, valve pls fix.

            //mientras no encuentre un carton apto, se repetira hasta que si.
            //como lo hace? jaja veni conmigo querido
            while ($flag == true) {
                //recorre cada columna del carton
                for ($j = 0; $j < 9; $j++) {
                    //si en esa columna, en las 3 filas hay 3 nulls o 3 numeros
                    //osea, columna solo de numeros o solo de null (PROHIBIDAS)
                    if (($matrix[1][$j] == null && $matrix[2][$j] == null && $matrix[0][$j] == null) || ($matrix[1][$j] != null && $matrix[2][$j] != null && $matrix[0][$j] != null)) {
                        //entonces tiro el for de vuelta a 0 y creo otro carton para comprobarlo
                        $j = 0;
                        $matrix = $algorithm->createCard();
                    } else {
                        //si llego al final sin entrar al if, corto el while porque ese carton messirve (?
                        if ($j == 8) {
                            $flag = false;
                        }
                    }
                }
            }

            $j = null;

            $string = null;

            //aca lo paso a string pa la db
            for ($f = 0; $f < 3; $f++) {
                for ($j = 0; $j < 9; $j++) {
                    $numero = $matrix[$f][$j];

                    if ($j == 8) {
                        if ($numero == null) {
                            $string .= "null//";
                        } else {
                            $string .= $numero . "//";
                        }
                    } else {
                        if ($numero == null) {
                            $string .= "null/";
                        } else {
                            $string .= $numero . "/";
                        }
                    }
                }
            }



            //y aca la mando a la db
            $this->model->InserBingoCard($id, $string);
        }
    }


    function createList()
    {
        $list_id = $this->model->InsertEmptyList();
        $this->createCard($list_id);
    }

    function Logout()
    {
        session_start();
        session_destroy();
        $this->view->ShowLoginLocation();
    }





























    function ABM()
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();
        $this->view->ShowABM();
    }

    function ArtworkABM()
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();

        $artworks = $this->modelArtwork->GetArtworkAndCategories();
        $categories = $this->modelCategory->GetCategories();
        $this->view->ShowArtworkABM($artworks, $categories);
    }

    function CategoryABM($params = null)
    {

        if ($params == null) {
            $message = null;
        } else $message = $params;

        $this->loginController->checkPermissions();
        $this->requestSessionInfo();
        $categories = $this->modelCategory->GetCategories();

        $this->view->ShowCategoryABM($categories, $message);
    }

    function UserABM()
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();
        $users = $this->modelUser->getAllUsers();

        //le paso el nombre para evitar que se edite a si mismo los permisos;
        $this->view->ShowUserABM($users, $_SESSION["USERNAME"]);
    }

    function AddCategoryToDB()
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();

        $nombre = $_POST["nombre"];

        //check que no esté vacío
        if ((isset($nombre)) && (strlen($nombre) > 0)) {
            $this->modelCategory->AddCategory($nombre);
            $this->view->ShowCategoryABMLocation();
        }
    }

    function AddArtworkToDB()
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();

        if ((isset($_POST["nombre"])) && (isset($_POST["descripcion"])) && (isset($_POST["autor"])) && (isset($_POST["anio"])) && (isset($_POST["category"]))) {

            $nombre = $_POST["nombre"];
            $descripcion = $_POST["descripcion"];
            $autor = $_POST["autor"];
            $anio = $_POST["anio"];
            $category = $_POST["category"];

            if (isset($_FILES)) {
                if ($_FILES['imagen']['type'] == "image/jpg" ||  $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {

                    //si la imagen tiene el formato aceptado, guardo 2 cosas en un array para pasarle al model:

                    //1) la ubicacion de la imagen cuando recien está subida y está en formato .temp ("fileTemp").

                    //2) el lugar donde quiero guardar la imagen. consiste de: la carpeta donde la quiero guardar,
                    // un nombre unico (uniqid) y la extension de la imagen. agrupo todo esto en un string.

                    $imagen["fileTemp"] = $_FILES['imagen']['tmp_name'];
                    $imagen["filePath"] = "temp/" . uniqid("", true) . "." . strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                } else {

                    //si quiso subir una imagen pero no era compatible, le doy una por default que tengo en la carpeta /temp.
                    $imagen["filePath"] = "temp/image-placeholder.png";
                }
                //si o si necesito darle una imagen por default
            } else $imagen["filePath"] = "temp/image-placeholder.png";

            $this->modelArtwork->AddArtwork($nombre, $descripcion, $autor, $anio, $imagen, $category);
            $this->view->ShowArtworkABMLocation();
        }
    }

    function SearchByCategory()
    {

        $this->requestSessionInfo();

        if (isset($_POST["category"]) && (strlen($_POST["category"]) > 0)) {

            $category_id = $_POST["category"];

            $artworks = $this->modelArtwork->GetArtworksByCategory($category_id);
            $category = $this->modelCategory->GetCategory($category_id);
            $categories = $this->modelCategory->GetCategories();

            //si no hay obras compatibles con esa busqueda, lo paso a null para no tener
            //que preguntar por arrays vacios en smarty(se podrá incluso?)
            if (empty($artworks)) $artworks = null;

            $this->view->showArtworksByCategory($artworks, $categories, $category);
        }
    }

    function Categories()
    {
        $this->requestSessionInfo();

        $categories = $this->modelCategory->GetCategories();
        $this->view->ShowAllCategories($categories);
    }





    function DeleteArtwork($params = null)
    {
        $obra_id = $params[':ID'];

        //compruebo que sea un numero 
        if (is_numeric($obra_id)) {
            $this->modelArtwork->DeleteArtwork($obra_id);
            $this->view->ShowArtworkABMLocation();
        }
    }

    function DeleteCategory($params = null)
    {
        $category_id = $params[':ID'];

        if (is_numeric($category_id)) {
            $result = $this->modelCategory->DeleteCategory($category_id);
            $this->view->ShowCategoryABMLocation();
        }
    }

    function DeleteUser($params = null)
    {
        $user_id = $params[':ID'];

        if (is_numeric($user_id)) {
            $affected = $this->modelUser->DeleteUser($user_id);

            //si no afecto a ninguna row, quiere decir que tiene comentarios asociados
            if ($affected == 0) {

                //agarro todos los id de comentarios que tenga asociados
                $comentarios = $this->modelComment->getCommentsIdbyUserId($user_id);


                foreach ($comentarios as $comentario) {
                    //por cada uno, le cambio el id de usuario por uno default
                    $this->modelComment->modifyUserId($comentario->comment_id, 0);
                }

                //y finalmente lo borra
                $resultado = $this->modelUser->DeleteUser($user_id);
            }

            $this->view->ShowUserABMLocation();
        }
    }



    function AddEditedArtwork($params = null)
    {

        if ((isset($_POST["nombre"])) && (isset($_POST["imagen_url"])) && (isset($_POST["descripcion"])) && (isset($_POST["autor"])) && (isset($_POST["anio"])) && (isset($_POST["category"]))) {

            $nombre = $_POST["nombre"];
            $descripcion = $_POST["descripcion"];
            $autor = $_POST["autor"];
            $anio = $_POST["anio"];
            $category = $_POST["category"];
            $imagen_url = $_POST["imagen_url"];

            if (isset($_FILES)) {
                if ($_FILES['imagen']['type'] == "image/jpg" ||  $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {

                    //si la imagen tiene el formato aceptado, guardo 2 cosas en un array para pasarle al model:

                    //1) la ubicacion de la imagen cuando recien está subida y está en formato .temp ("fileTemp").

                    //2) el lugar donde quiero guardar la imagen. consiste de: la carpeta donde la quiero guardar,
                    // un nombre unico (uniqid) y la extension de la imagen. agrupo todo esto en un string.

                    $imagen["fileTemp"] = $_FILES['imagen']['tmp_name'];
                    $imagen["filePath"] = "temp/" . uniqid("", true) . "." . strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                } else {

                    //si quiso subir una imagen pero no era compatible, le doy le que ya tenia.
                    $imagen["filePath"] =  $imagen_url;
                }
            } else $imagen["filePath"] = $imagen_url;

            $obra_id = $params[':ID'];

            if (is_numeric($obra_id)) {
                $this->modelArtwork->UpdateArtwork($nombre, $descripcion, $autor, $anio, $imagen, $category, $obra_id);
                $this->view->ShowArtworkABMLocation();
            }
        }
    }

    function AddEditedCategory($params = null)
    {
        $categoria_id = $params[':ID'];

        if ((is_numeric($categoria_id)) && (isset($_POST["nombre"]))) {

            $this->modelCategory->UpdateCategory($categoria_id, $_POST["nombre"]);
            $this->view->ShowCategoryABMLocation();
        }
    }

    function AddEditedUser($params = null)
    {

        $user_id = $params[':ID'];

        if ((is_numeric($user_id)) && (isset($_POST["nombre"])) && (isset($_POST["admin_auth"]))) {

            $nombre = $_POST["nombre"];
            $auth = $_POST["admin_auth"];

            $this->modelUser->UpdateUser($user_id, $nombre, $auth);
            $this->view->ShowUserABMLocation();
        }
    }

    function ArtworkEdit($params = null)
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();

        $obra_id = $params[':ID'];

        if (is_numeric($obra_id)) {
            $artwork = $this->modelArtwork->GetArtwork($obra_id);
            $categories = $this->modelCategory->GetCategories();
            $this->view->ShowArtEdit($artwork, $categories);
        }
    }

    function CategoryEdit($params = null)
    {
        $this->loginController->checkPermissions();

        $category_id = $params[':ID'];

        if (is_numeric($category_id)) {
            $artwork = $this->modelCategory->GetCategory($category_id);
            $this->view->ShowCategoryEdit($artwork);
        }
    }


    function UserEdit($params = null)
    {
        $this->loginController->checkPermissions();
        $this->requestSessionInfo();

        $user_id = $params[':ID'];

        if (is_numeric($user_id)) {
            $user = $this->modelUser->GetAllById($user_id);
            $this->view->ShowUserEdit($user);
        }
    }


    function Details($params = null)
    {
        $this->requestSessionInfo();

        $obra_id = $params[':ID'];

        if (is_numeric($obra_id)) {
            $artwork = $this->modelArtwork->GetArtworkAndCategoryById($obra_id);
            $this->view->ShowDetails($artwork);
        }
    }

    function Artworks()
    {
        $this->requestSessionInfo();

        $artworks = $this->modelArtwork->GetArtworkAndCategories();
        $categories = $this->modelCategory->GetCategories();

        $this->view->ShowAllArtworks($artworks, $categories);
    }



    function paginatedArtworks($params = null)
    {
        $this->requestSessionInfo();

        //no es una id, pero es el offset que tomo de base
        $offset_number = $params[':ID'];

        //lo derivo a otra funcion para poder cambiar la cantidad que quiero que se muestren en pantalla
        // lo pongo por defecto en 3, pero se pueden poner las que quieras
        $this->startPagination($offset_number, 3);
    }

    function startPagination($offset, $quantity)
    {
        //creo el limite para el LIMIT de sql 
        $next_offset = $offset + $quantity;

        //compruebo si puedo crear el $back_offset sin que el numero que se crea sea negativo
        if ($offset > 0) {
            $back_offset = $offset - $quantity;
        } else {
            //le doy esto porque smarty no sabe diferenciar 0 de null.
            //se lo paso asi para evitar crear el boton cuando no se puede retroceder mas
            $back_offset = 0.1;
        }

        //le doy de desde donde ($offset) hasta donde ($limit) me traiga obras
        $artworks = $this->modelArtwork->getBlockOfArtworks($offset, $quantity);

        //agarrar la cantidad de rows. arranca desde 0
        $rowcount = $this->modelArtwork->GetRowCount();
        $rowcount_number = $rowcount["COUNT(*)"];


        //si el limite supera o es igual a la cantidad de rows de la db, le doy null para evitar
        //que se pueda crear el boton de avanzar, ya que no traeria nada de la db
        if ($next_offset >= $rowcount_number) {
            $next_offset = null;
        }
        $this->view->showPaginatedPage($artworks, $next_offset, $back_offset);
    }
}
