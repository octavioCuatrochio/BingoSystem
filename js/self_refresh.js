document.addEventListener("DOMContentLoaded", () => {

    let MyInfo;
    // getUserInfo();
    getBingoCards();
    let interval_id;

    let numbers;
    let mother_matrix = [];
    let number_count = 0;
    let last_number = {
        id: null
    }


    function getRoundInfo() {
        fetch("api/ronda", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {

                            if (json.estado != 0) {
                                clearInterval(interval_id);
                                getBingoCards();
                            }
                        }
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

    function getUserInfo() {
        fetch("api/usuario", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {

                            MyInfo = json;
                            interval_id = setInterval(getRoundInfo, 1000);
                        }
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

    function getBingoCards() {
        fetch("api/carton", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        // console.log(mother_matrix);

                        if (typeof json.message == 'undefined') {
                            //a ver por donde arranco

                            //por cada carton
                            console.log(json);
                            json.forEach(carton => {

                                // MarkOwner(MyInfo, carton.id);

                                let i = 0;
                                let matrix = [];
                                let table_id = carton.id;

                                let array = carton.numeros.split("//");
                                array.pop();

                                array.forEach(element => {
                                    matrix[i] = element.split("/");
                                    i++;
                                });

                                //desenvuelvo el array con splits.

                                //guardo una matriz en una clave de la matriz madre
                                mother_matrix[carton.id] = matrix;


                                //se lo doy para que lo muestre
                                displayCard(matrix, table_id);
                            });

                            // getNumbers();

                            // StartGame(mother_matrix);

                        }
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

    function MarkOwner(Info, id) {

        let data = {
            dni: Info.dni,
        };

        fetch("api/mark/carton/" + id, {
            'method': 'POST',
            'headers': {
                'Content-Type': 'application/json'
            },
            'body': JSON.stringify(data)
        })
            .then(response => response.json())
            .then(comment => console.log(comment))
            .catch(error => console.log(error));
    }

    function StartGame(matrix) {

        startIndex(mother_matrix);
        interval_id = setInterval(checkStatus, 20000);
    }

    function checkStatus() {

        fetch("api/ganador-linea", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {

                            if (json.ganador_linea == null) {

                                lookForNumber(json.numero, mother_matrix, number_count, MyInfo);
                                number_count++;

                            } else clearInterval(interval_id);
                        }
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


    function getNumbers() {
        fetch("api/numeros", {
            'method': 'GET',
        })
            .then(response => {
                response.json().then(json => {
                    if (response.status == "200") {

                        if (typeof json.message == 'undefined') {

                            numbers = json.numeros;
                            console.log(numbers);

                        }
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

});
