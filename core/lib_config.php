<?php

//Configuración específica de PHP //
//********************************//

//Zona horaria
date_default_timezone_set(G_ZONE);

//Errores
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL | E_STRICT);  // Para Desarrollo

function GestionDeExcepciones($excepcion)
{
	//Determinamos si es Excepción o Error
	if ( strpos($excepcion,G_GESTERRS) == 1)
	{
		//Error
		$params = explode(G_SEPPARERR,$excepcion);
		$codigo = $params[1];
		$texto = $params[2];
		$archivo = $params[3];
		$linea = $params[4];
		$var = $params[5];

		//Definimos una matriz asociativa de cadenas de error
		$tipoerror = array (
			0   => G_ERMSJ,
			1   =>  G_ERERROR,
			2   =>  G_ERADV,
			4   =>  G_ERINTERP,
			8   =>  G_ERAVISO,
			16  =>  G_ERNUCLEO,
			32  =>  G_ERADVNUC,
			64  =>  G_ERCOMP,
			128 =>  G_ERADVCOMP,
			256 =>  G_ERUSER,
			512 =>  G_ERADV,
			1024=>  G_ERADVUSER,
			2048=>  G_ERERROR
		);
		$tipoerrortxt = $codigo.G_SEPTXTERR.$tipoerror[$codigo];
	}else{
		//Excepción
		$codigo = '';
		$texto = $excepcion->getMessage();
		$archivo = '';
		$linea = '';
		$var = '';
		$tipoerrortxt = G_EREXCCOD;
	}

	//Guardamos la fecha y la hora para registro de error y determinamos el usuario
	$fecha = date(G_FFECHAHORA);
	if(isset($_SESSION['usuariologin']))
		$usererr = $_SESSION['usuariologin'];
	else
		$usererr =G_ERUSERGUEST;

	//Conjunto de errores de los cuales se almacenara un rastreo
	$erroresdeusuario = array(E_USER_ERROR, E_USER_WARNING);

	//Registramos el error en el fichero de log
	if ( $_SERVER['PHP_SELF'] <> G_INDEX )
		$flog = G_BARRAPADRE.G_FILELOG;
	else
		$flog = G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_FILELOG;
	$f = fopen($flog,'a');
	fwrite($f,"[$fecha] User:$usererr Type:$tipoerrortxt File:$archivo Called File:".$_SERVER['PHP_SELF']." Line:$linea\r\n");
	fwrite($f,"$texto\r\n");
	fwrite($f,"\r\n");
	if (in_array($codigo, $erroresdeusuario))
		//Devuelve en un string la estructura de datos de $var
		fwrite($f,"Vars  :".wddx_serialize_value($var)."\r\n");
	fwrite($f,"\r\n");
	fclose($f);

	//Mostramos el error al usuario
	echo "<div align=center style='border:0px solid #FF0000; padding:20px;'>".
		 "<table width=600px cellpadding=2 cellspacing=0 border=1 borderColor=#AAAAAA style='border-collapse:collapse;'>\n".
	     "<tr><td bgcolor=#ECECEC colspan=2 align=left><font style='font-family:Trebuchet MS; font-size:12px; font-style:normal; font-weight:bold; color:#FF0000; text-decoration:underline;'><u>Se ha producido el siguiente Errror:</u></font></td></tr>\n".
		 "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Usuario:<b></td><td>$usererr</td></tr>\n".
		 "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Fecha/Hora:<b></td><td>$fecha</td></tr>\n".
		 "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Tipo:<b></td><td>$tipoerrortxt</td></tr>\n";
	if ( empty($linea) )
		echo "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Archivo:<b></td><td>$archivo</td></tr>\n";
	else
		echo "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Archivo:<b></td><td>$archivo linea $linea</td></tr>\n";
	echo "<tr style='font-family:Trebuchet MS; font-size:11px; font-style:normal; font-weight:normal; color:#000000;'><td align=left valign=top width=75px><b>Descripción:<b></td><td>$texto</td></tr></table></div>\n";
	exit();
}

set_exception_handler('GestionDeExcepciones');

function GestionDeErrores($codigo, $texto, $archivo, $linea, $vars)
{
	GestionDeExcepciones('_'.G_GESTERRS.G_SEPPARERR.$codigo.G_SEPPARERR.$texto.G_SEPPARERR.$archivo.G_SEPPARERR.$linea.G_SEPPARERR.$vars);
	exit;
}

set_error_handler('GestionDeErrores');

?>
