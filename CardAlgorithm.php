<?php

class CardAlgorithm
{

    function __construct()
    {
    }

    function createCard()
    {

        $carton = null;
        $anterior = null;

        //arranco el array
        for ($i = 0; $i < 3; $i++) {
            $espacios[$i] = null;
        }

        //nota: el rand hace de tal a tal numero INCLUSIVE
        for ($i = 0; $i <  3; $i++) {

            for ($j = 0; $j < 9; $j++) {

                $retorno = $this->returnRandom($j);

                //si todavia no llego al tope...
                if ($espacios[$i] < 4) {

                    $espacio = rand(0, 1);

                    //si el anterior no era null...(para darle variedad)
                    if ($anterior != null) {

                        if ($espacio == 1) {
                            $espacios[$i]++;
                            $retorno = null;
                        }
                    }
                }
                $anterior = $retorno;

                $carton[$i][$j] = $retorno;
            }
        }

        //pongo los espacios vacios que falten. PUEDE que falte 1 espacio en ocaciones. pero por ahora piola
        //pd: recorro por fila
        for ($i = 0; $i < 3; $i++) {

            //si no cumplio el tope, lo hago hasta que llegue
            if ($espacios[$i] < 4) {
                for ($j = $espacios[$i]; $j < 4; $j++) {

                    $offset = rand(0, 8);

                    //si en el que cae es null, busco otro de vuelta
                    if ($carton[$i][$offset] == null) {

                        $offset = rand(0, 8);

                        //mientras que encuentre un null siga haciendo rand (asi me garantizo que esten los 4)
                        while ($carton[$i][$offset] == null) {
                            $offset = rand(0, 8);
                        }

                        //y bueno, si este tambien era null jodete
                        $carton[$i][$offset] = null;
                    } else $carton[$i][$offset] = null;
                }
            }
        }

        $this->checkRepeatedNumbers($carton);

        $this->checkEmptyColummns($carton);

        //y una ultima vez...repaso por si faltan cosos en blanco?
        $this->addSpaces($carton);

        // de aca para abajo es para corregir (opcional)


        // $this->checkNumberColummns($carton);

        $this->checkEmptyColummns($carton);

        // $this->addSpaces($carton);

        //de aca para abajo ya no hay tanta mejora


        $this->checkRepeatedNumbers($carton);

        // $this->checkNumberColummns($carton);

        $this->addSpaces($carton);

        // $this->checkNumberColummns($carton);

        return $carton;
    }



    function returnRandom($offset)
    {
        $retorno = null;

        switch ($offset) {
            case '0':
                $retorno = rand(1, 9);
                break;

            case '1':
                $retorno = rand(10, 19);
                break;

            case '2':
                $retorno = rand(20, 29);
                break;

            case '3':
                $retorno = rand(30, 39);
                break;

            case '4':
                $retorno = rand(40, 49);
                break;


            case '5':
                $retorno = rand(50, 59);
                break;


            case '6':
                $retorno = rand(60, 69);
                break;


            case '7':
                $retorno = rand(70, 79);
                break;


            case '8':
                $retorno = rand(80, 89);
                break;
        }
        return $retorno;
    }

    function  addSpaces(&$matrix)
    {

        for ($i = 0; $i < 3; $i++) {

            $counter = 0;
            for ($h = 0; $h < 9; $h++) {
                if ($matrix[$i][$h] == null) {
                    $counter++;
                }
            }

            //si no cumplio el tope, lo hago hasta que llegue
            if ($counter < 4) {
                for ($counter; $counter < 4; $counter++) {

                    $offset = rand(0, 8);

                    //si en el que cae es null, busco otro de vuelta
                    if ($matrix[$i][$offset] == null) {

                        $offset = rand(0, 8);

                        //mientras que encuentre un null siga haciendo rand (asi me garantizo que esten los 4)
                        while ($matrix[$i][$offset] == null) {
                            $offset = rand(0, 8);
                        }

                        //y bueno, si este tambien era null jodete
                        $matrix[$i][$offset] = null;
                    } else $matrix[$i][$offset] = null;
                }
            }
        }
    }

    function checkEmptyColummns(&$matrix)
    {
        //pa que no haya columnas en blanco
        for ($i = 0; $i < 9; $i++) {

            $base = $matrix[1][$i];

            //si la columna es toda compuesta de nulls
            if ($matrix[2][$i] == null && $base == null && $matrix[0][$i] == null) {
                //si el que tiene delante no es null...
                if ($matrix[1][$i + 1] != null) {
                    $matrix[1][$i + 1] = null;
                    $matrix[1][$i] = $this->returnRandom($i);
                    //el de adelante es null. si el que tiene detras no es null...
                } else if ($matrix[1][$i - 1] != null) {
                    $matrix[1][$i - 1] = null;
                    $matrix[1][$i] = $this->returnRandom($i);
                }
                // else {
                //     if ($matrix[1][$i + 2] != null) {
                //         $matrix[1][$i + 2] = null;
                //         $matrix[1][$i] = $this->returnRandom($i);
                //     } else {
                //         $matrix[1][$i - 2] = null;
                //         $matrix[1][$i] = $this->returnRandom($i);
                //     }
                // }
            }
        }
    }

    function checkNumberColummns(&$matrix)
    {
        //para evitar que se formen columnas de numeros
        for ($i = 0; $i < 9; $i++) {

            $base = $matrix[1][$i];

            //si la columna es toda compuesta de nulls
            if ($matrix[2][$i] != null && $base != null && $matrix[0][$i] != null) {
                //si el que tiene delante no es null...
                if ($matrix[1][$i + 1] == null) {
                    $matrix[1][$i + 1] = $this->returnRandom($i + 1);
                    $matrix[1][$i] = null;
                    //el de adelante es null. si el que tiene detras no es null...
                } else if ($matrix[1][$i - 1] == null) {
                    $matrix[1][$i - 1] = $this->returnRandom($i + 1);
                    $matrix[1][$i] = null;
                }
            }
        }
    }

    function checkRepeatedNumbers(&$matrix)
    {
        //pa sacar repetidos
        for ($i = 0; $i < 9; $i++) {

            $base = $matrix[1][$i];

            for ($j = 0; $j < 2; $j++) {

                if ($base != null) {
                    if ($matrix[2][$i] == $base) {
                        $matrix[2][$i]++;
                    }
                    if ($matrix[0][$i] == $base) {
                        $matrix[0][$i]++;
                    }
                    if ($matrix[2][$i] == $matrix[0][$i]) {
                        $matrix[2][$i] = $this->returnRandom($i);
                    }
                }
            }
        }
    }
}
