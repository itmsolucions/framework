<?php

//Funciones Generales//
//*******************//

//Función que convierte la cadena $cad en Minusculas
function CadenaMinusculas($cad)
{
	return strtolower($cad);
}

//Función que convierte el vector $vector en minusculas
function VectorMinusculas($vector)
{
	//Aplicamos la función 'CadenaMinusculas' a cada miembro del vector
	array_walk($vector, 'CadenaMinusculas');
	return $vector;
}

//Función que convierte la cadena a la salida correspondiente (En función del Servidor)
function CadenaConvertida($cad)
{
	return utf8_decode($cad);
}

//Función que convierte la cadena a la entrada correspondiente (En función del Servidor)
function ConvierteCadena($cad)
{
	return utf8_encode($cad);
}

//Función que formatea a 2 decimales
function Fmt2Decs($cad)
{
	$cad = number_format($cad,2,G_SEPDEC,G_SEPMIL);
	return $cad;
}

//Función que formatea números enteros
function FmtEnteros($cad)
{
	$cad = number_format($cad,0,G_SEPDEC,G_SEPMIL);
	return $cad;
}

//Función que formatea una fecha
function FmtFecha($cad)
{
	return date_format(date_create($cad),G_FFECHA);
}

//Función que formatea una Hora
function FmtHora($cad)
{
	return date_format(date_create($cad),G_FHORA);
}

//Función que formatea una Fecha/Hora
function FmtFechaHora($cad)
{
	return date_format(date_create($cad),G_FFECHAHORA);
}

//Función que verifica si una dirección de e-mail tiene el formato correcto
function ComprobarEmail($email){
    $mail_correcto = 0;
    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
        //compruebo unas cosas primeras
        if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
            //miro si tiene caracter .
            if (substr_count($email,".")>= 1){
                //obtengo la terminacion del dominio
                $term_dom = substr(strrchr ($email, '.'),1);
                //compruebo que la terminación del dominio sea correcta
                if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                    //compruebo que lo de antes del dominio sea correcto
                    $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                    $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                    if ($caracter_ult != "@" && $caracter_ult != "."){
                        $mail_correcto = 1;
                    }
                 }
            }
        }
    }

    if ($mail_correcto)
        return 1;
    else
        return 0;
}

?>