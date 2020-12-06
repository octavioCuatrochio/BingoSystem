<?php
require_once './Model/Model.php';
require_once 'ApiController.php';

class ApiBingoController extends ApiController
{

    function __construct()
    {
        parent::__construct();
        $this->model = new Model();
        $this->view = new APIView();
    }


    public function GetCommentsByArtworkId($params = null)
    {
        $id = $params[':ID'];

        if ($id != null && is_numeric($id)) {
            $comments = $this->model->getAllByArtworkId($id);

            // verifica si los comentarios existen
            if (!empty($comments)) {
                $this->view->response($comments, 200);
            } else {
                $obj = (object) array('message' => "La obra con el id=$id no tiene comentarios");
                $this->view->response($obj, 200);
                die();
            }
        } else {
            $obj = (object) array('message' => "La obra no existe o el id es incorrecto");
            $this->view->response($obj, 404);
            die();
        }
    }

    function GetSessionState()
    {

        $state = $this->model->GetEstadoDeJuego();

        if (!empty($state)) {
            $this->view->response($state, 200);
        } else {
            $obj = (object) array('message' => "El juego no existe");
            $this->view->response($obj, 404);
            die();
        }
    }

    function GetBingoCards()
    {

        $cards = $this->model->GetBingoCards();

        if (!empty($cards)) {

            foreach ($cards as $card) {
                //activarrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
                // $this->model->MarkCardAsUsed($card->id);
            }

            $this->view->response($cards, 200);
        } else {
            $obj = (object) array('message' => "No hay cartones disponibles");
            $this->view->response($obj, 404);
            die();
        }
    }

    function GetNumbers()
    {
        $state = $this->model->getNumbers();

        if (!empty($state)) {
            $this->view->response($state, 200);
        } else {
            $obj = (object) array('message' => "No hay numeros");
            $this->view->response($obj, 404);
            die();
        }
    }

    function GetLineWinner()
    {
        $winner = $this->model->GetLineWinner();

        if (!empty($winner)) {
            $this->view->response($winner, 200);
        } else {
            $obj = (object) array('message' => "El juego no existe");
            $this->view->response($obj, 404);
            die();
        }
    }

    function GetBingoWinner()
    {
        $winner = $this->model->GetBingoWinner();

        if (!empty($winner)) {
            $this->view->response($winner, 200);
        } else {
            $obj = (object) array('message' => "El juego no existe");
            $this->view->response($obj, 404);
            die();
        }
    }

    function MarkLineWinner()
    {

        $body = $this->getData();

        $result = $this->model->addLineWinner($body->dni);

        if ($result > 0) {
            $this->view->response("The line winner was marked.", 200);
        } else {
            $this->view->response("The line winner wasn't marked.", 404);
        }
    }

    function MarkBingoWinner()
    {

        $body = $this->getData();

        $result = $this->model->addBingoWinner($body->dni);

        if ($result > 0) {
            $this->view->response("The bingo winner was marked.", 200);
        } else {
            $this->view->response("The bingo winner wasn't marked.", 404);
        }
    }



    function GetUserData()
    {

        if (isset($_SESSION["DNI_USER"])) {
            $id = $_SESSION["DNI_USER"];
        } else {
            session_start();
            if (isset($_SESSION["DNI_USER"])) {
                $id = $_SESSION["DNI_USER"];
            } else {
                $obj = (object) array('message' => "El usuario no está loggeado");
                $this->view->response($obj, 200);
                die();
            }
        }

        $user = $this->model->getByDNI($id);
        if (!empty($user)) {
            $this->view->response($user, 200);
        } else {
            $obj = (object) array('message' => "El usuario no existe");
            $this->view->response($obj, 404);
            die();
        }
    }

    public function InsertComment($params = null)
    {
        $body = $this->getData();

        // eh?
        $comment_id = $this->model->InsertComment($body->text, $body->rating, $body->artwork_id, $body->user_comment_id);

        if (!empty($comment_id)) // verifica si la tarea existe
            $this->view->response($this->model->getCommentById($comment_id), 201);
        else
            $this->view->response("La tarea no se pudo insertar", 404);
    }

    function MarkOwnerBingoCard($params = null)
    {
        $id = $params[':ID'];

        $body = $this->getData();

        if ($id != null && is_numeric($id)) {

            $result = $this->model->addOwnerToCard($id, $body->dni);

            if ($result > 0) {
                $this->view->response("The card was marked.", 200);
            } else {
                $this->view->response("The card wasn't marked.", 404);
            }
        } else $this->view->response("The card doesn't exist.", 404);
    }
}
