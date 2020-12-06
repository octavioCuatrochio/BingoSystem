
let matrix_index = [];
let lineFlag = false;
let bingoFlag = false;

function startIndex(matrix) {

    for (let mini_matrix_key in matrix) {

        matrix_index[mini_matrix_key] = [];

        for (let i = 0; i < 3; i++) {

            matrix_index[mini_matrix_key][i] = 0;
        }
    };
}

function lookForNumber(numero, matrix, count, info) {

    //le pongo un index a ubication para guardar las coordenadas necesarias, va a sumar por cada
    //coordenada agregada
    let for_ubication_index = 0;

    //arranco el array que va a tener las coordenadas
    let ubication = [];

    //este for significa: por cada clave en la matriz...
    for (let mini_matrix_key in matrix) {

        //guardo el carton que tiene esa clave para usarlo como referencia (a falta del foreach de php...)
        let mini_matrix = matrix[mini_matrix_key];

        //recorro el carton
        for (let i = 0; i < 3; i++) {
            for (let j = 0; j < 9; j++) {

                //si esa posicion tiene ese numero...
                if (mini_matrix[i][j] == numero) {

                    //guarda la coordenada en string
                    ubication[for_ubication_index] = mini_matrix_key + "," + i + "," + j;

                    mini_matrix[i][j] = "X";

                    for_ubication_index++;
                }
            }
        }
    };

    //aca iria todo el algoritmo de comprobar los patrones y eso. paja porque no se las reglas.

    //cuando se tiene 5 o mas numeros, arranca a comprobar por patrones
    if (count >= 5) {
        searchPatterns(matrix, ubication, count, info);
    }

    //me transforma de coordenadas en string a un numero que se traslada a id
    let ids = translateToDOM(ubication);
    crossOutNumber(ids);
}

//lo comento asi no me pierdo na
function translateToDOM(coordenadas) {

    let aux_id = [];
    let i = 0;
    //por cada coordenada traida en string
    coordenadas.forEach(coordenada => {

        //la paso a array
        let aux = coordenada.split(",");
        let resultado = 0;
        parseInt(resultado);

        //si el carton era el primero, no sumo nada
        if (aux[0] != "1") {

            //si en cambio era el segundo carton, le sumo 27 del primero
            if (aux[0] == "2") {
                resultado += 27;
            } else {
                //y si ya no es ninguno de esos, multiplico la cantidad de cartones anteriores al actual * 27
                resultado = (aux[0] - 1) * 27;
            }
        }

        //si la fila esta en 0, no pasa na
        switch (aux[1]) {

            //si esta en la segunda fila, le sumo los 9 de la primera fila
            case "1":
                resultado += 9;
                break;

            //si esta en la ultima fila, le sumo los 18 de las anteriores
            case "2":
                resultado += 18;
                break;
        }

        //y finalmente le sumo la columna
        resultado += parseInt(aux[2]);

        aux_id[i] = resultado;
        i++;
    });

    return aux_id;

}

function searchPatterns(matrix, coordenates, count, info) {

    coordenates.forEach(coordenate => {
        //[0] eq carton, [1] eq fila y [2] eq columna
        array = coordenate.split(",");

        let counter = 0;

        //recorro toda la fila
        for (let i = 0; i < 9; i++) {

            //en base al ultimo colocado, recorro toda esa fila
            if (matrix[array[0]][array[1]][counter] == "X") {
                counter++;
            }
        }

        //si los 5 numeros que encontro eran "X", quiere decir que hay una linea
        if (counter == 5) {

            //asigna en la matriz auxiliar la fila que se creo
            matrix_index[array[0]][array[1]] = 1;

            //si todavia no existe un ganador de linea y ya salieron mas de 5 numeros...
            if (lineFlag == false && count >= 5) {

                fetch("api/ganador-linea", {
                    'method': 'GET',
                })
                    .then(response => {
                        response.json().then(json => {
                            if (response.status == "200") {

                                if (typeof json.message == 'undefined') {


                                    //pregunto si ya hay un ganador, si no lo hay mando mis datos
                                    if (json.ganador_linea == null) {
                                        sendWinner(info.dni, "line");

                                        //y pongo la flag en true, para que no vuelva a preguntar
                                        // a la db si ya hay ganadores
                                    }
                                    lineFlag = true;
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

                //si ya hay ganador y ya salieron mas de 15 numeros
            } else if (bingoFlag == false && count >= 15) {

                let j = 0;

                //dentro del carton en donde marqué la fila,
                //cuento la cantidad de filas marcadas que habia en la matriz auxiliar
                //dentro de ese carton
                matrix_index[array[0]].forEach(row => {
                    if (row == 1) {
                        j++;
                    }
                });

                //si las 3 filas esta llenas...
                if (j == 3) {
                    fetch("api/ganador-bingo", {
                        'method': 'GET',
                    })
                        .then(response => {
                            response.json().then(json => {
                                if (response.status == "200") {

                                    if (typeof json.message == 'undefined') {

                                        //pregunto si habia ganador, si no mando mis datos
                                        if (json.ganador_carton == null) sendWinner(info.dni, "bingo");

                                        bingoFlag = true;
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
            }
        }
    });
}

function sendWinner(dni, option) {
    let data = {
        dni: dni,
    };

    fetch("api/mark/" + option, {
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



