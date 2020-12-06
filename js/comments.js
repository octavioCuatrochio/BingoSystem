document.addEventListener("DOMContentLoaded", () => {

    //por ahora asi puedo pedir el id de la obra 
    let ubication = window.location.pathname;
    let ubication_array = ubication.split('/');
    let art_id = ubication_array[ubication_array.length - 1];


    let userInfo = null;

    //hacer llamada a la api, me fui a jugar al rocket.
    getUserInfo();



    //DIO MIO KESESTO
    function createCommentInput() {
        //Container principal
        let input_container = document.querySelector("#comment_input_container");

        //le creo la barrita
        let divider = document.createElement("hr");
        divider.classList.add("divider");

        input_container.appendChild(divider);

        //Creo el input para el comentario
        let input = document.createElement("textarea");
        input.classList.add("comment_input");
        input.id = "comment_input_area";

        //container que va a almacenar el boton y el select
        let input_container_functions = document.createElement("div");
        input_container_functions.classList.add("input_container_functions");

        //creo el boton
        let btn = document.createElement("button");
        btn.classList.add("register_btn");
        btn.id = "comment_input_button";
        btn.innerHTML = "Comentar";
        btn.addEventListener("click", addComment);

        //creo el container para el select y el texto("Calificación")
        let rating_container = document.createElement("div");


        let text = document.createElement("h6");
        text.innerHTML = "Calificación: ";

        //creo el select
        let selectList = document.createElement("select");
        selectList.id = "comment_select_list";

        //creo los options del select del 1 al 5 y los añado
        for (let i = 1; i <= 5; i++) {
            let option = document.createElement("option");
            option.value = i;
            option.text = i;
            selectList.appendChild(option);
        }

        rating_container.appendChild(text);
        rating_container.appendChild(selectList);

        input_container_functions.appendChild(rating_container);
        input_container_functions.appendChild(btn);

        input_container.appendChild(input);
        input_container.appendChild(input_container_functions);


    }

    function createComment(comentario) {

        //agarro el ul del DOM
        let comment_list = document.querySelector("#comment_list");

        //creo un li para arrancar a añadirle cosas
        let comment_container = document.createElement("li");
        comment_container.classList.add("comment_container");

        //div que va a contener el nombre de usuario y el rating
        let comment_user_container = document.createElement("div");
        comment_user_container.classList.add("comment_user_container");

        //creo el nombre
        let comment_name = document.createElement("h7");
        comment_name.innerHTML = comentario.nombre;
        comment_name.classList.add("comment_name");

        //creo la calificación
        let comment_rating = document.createElement("h8");
        comment_rating.innerHTML = comentario.rating;

        let comment_rating_outline = document.createElement("div");
        comment_rating_outline.classList.add("comment_rating_outline");

        comment_rating_outline.appendChild(comment_rating);

        comment_user_container.appendChild(comment_name);
        comment_user_container.appendChild(comment_rating_outline);

        //si la variable no cambio quiere decir que no está registrado
        if (userInfo != null) {
            //si es admin le doy el boton de borrar
            if (userInfo.admin_auth == "1") {

                let delete_btn = document.createElement("button");
                delete_btn.classList.add("register_btn", "comment_delete_btn");
                delete_btn.id = comentario.comment_id;
                delete_btn.innerHTML = "Borrar";

                //le doy el add event listener para eliminarse a si mismo
                delete_btn.addEventListener("click", deleteComment);

                comment_user_container.appendChild(delete_btn);
            }
        }

        //creo el texto principal del comentario
        let comment_text = document.createElement("h9");
        comment_text.innerHTML = comentario.text;
        comment_text.classList.add("comment_main_text");


        comment_container.appendChild(comment_user_container);
        comment_container.appendChild(comment_text);

        comment_list.appendChild(comment_container);


        // y finalmente creo un divisor para que no se choquen entre si,
        let divider = document.createElement("hr");
        divider.classList.add("divider_transparent");

        comment_list.appendChild(divider);
    }

    function getUserInfo() {
        fetch("api/usuario", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {
                            userInfo = json;

                            //comprueba que privilegios tenes
                            if (json.admin_auth == "1" || json.admin_auth == "0") {
                                createCommentInput();
                            }
                        }

                        getComments();
                    }
                    else {
                        console.log("Error no conocido.");
                    }
                })
            })
            .catch(function (e) {
                console.log("Error, no hay internet o no hay datos disponibles");
            })
    }

    function deleteComment() {

        //recorro el arbol del DOM para eliminar el comentario de la vista
        let id = this.id;
        let container = this.parentElement;
        let comment = container.parentElement;
        let divider = comment.nextElementSibling;

        deleteCommentById(id);
        //remuevo el nodo del DOM
        comment.remove();
        divider.remove();
    }

    function getComments() {
        fetch("api/obras/" + art_id, {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {

                            for (let comentario of json) {
                                createComment(comentario);
                            }
                        }
                        else {
                            console.log("Error no conocido.");
                        }
                    }
                })
            })
            .catch(function (e) {
                console.log("Error, no hay internet.");
            })
    }


    function addComment() {
        event.preventDefault();

        let comment = {
            text: document.querySelector('#comment_input_area').value,
            rating: document.querySelector('#comment_select_list').value,
            artwork_id: art_id,
            user_comment_id: userInfo.id
        }

        fetch("api/obras/" + art_id, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(comment)
        })
            .then(response => response.json())
            .then(comment => createComment(comment))
            .catch(error => console.log(error));

    }

    function deleteCommentById(id) {

        //hay que pasarle la id
        fetch("api/comentarios/" + id, {
            'method': 'DELETE',
        })
            //devuelve el comrpobante del envio
            .then(response => response.json())
            .then(json => console.log(json))
            .catch(error => console.log(error));

    }
});