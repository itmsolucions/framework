<?php

//Clase para tratar las plantillas//
//********************************//

//Evitamos que se llame directamente desde otra ubicación ajena al framework
if ( ! defined('G_FWITM') )
{
	throw new Exception(G_ERRCLSTEMP);
	exit();
}

//Clase administradora de plantillas
class cls_template {

	//Atributos Basicos de la clase//
	//-----------------------------//
 	private $page;												//Variable para almacenar la página

	//Operaciones de la clase//
	//-----------------------//

	//Constructor
	public function __construct()
	{
		//Creamos un objeto de la clase cls_pageadmin para gestionar la página, pero diferenciamos si estamos en pagina principal o en el resto del Proyecto
		if ( $_SERVER['PHP_SELF'] == G_INDEX )
			include(G_OBJS.G_CLSPAGEADMIN.G_EXT);
		else
			include(G_BARRAPADRE.G_BARRAPADRE.G_BARRAPADRE.G_OBJS.G_CLSPAGEADMIN.G_EXT);
		$this->page = new cls_pageadmin();
	}

	//Almacena en la clase 'page' el contenido de la plantilla pasada por parámetro
	public function buildFromTemplate($ltpl, $title)
	{
		if ( $_SERVER['PHP_SELF'] == G_INDEX )
			$pathtpl = G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_SKINS.cls_framework::getSetting(G_IDXSKIN).G_BARRA;
    	else
	    	$pathtpl = G_BARRAPADRE.G_SKINS.cls_framework::getSetting(G_IDXSKIN).G_BARRA;
		if( file_exists($pathtpl.G_TEMPLATES.$ltpl) == true ){
			//Agregamos la cabecera Standar
			$content = $this->setHeaderSt($pathtpl);
			//Agregamos la cabecera del Skin
			$content .= SkinHeader($pathtpl, $title);
			//Transmite el archivo entero a la variable $content y luego se guarda en la clase page
			$content .= file_get_contents($pathtpl.G_TEMPLATES.$ltpl);
			//Agregamos el pie del Skin
			$content .= SkinFooter($pathtpl, $title);
			//Agregamos el Pie Standar
			$content .= $this->setFooterSt();
			//Guardamos la pagina en la clase Page
			$this->page->setContent($content);
		}else
			throw new Exception(G_ERRELOADTEMPLATE.$ltpl);
    }

    //Obtiene el objeto página
	public function getPage()
	{
		return $this->page;
	}

	//Reemplaza los tag por datos
	public function parseOutput($title)
	{
		//Reemplazamos bloques de texto
		$this->replaceBloqs();
		//Reemplazamos Tags
		$this->replaceTags();
		//Establecemos Titulo
		$this->setTitle($title);
		//Reemplazamos Cadenas Standar
		$this->setStTags();
	}

	//Reemplaza cada tag con bloques de texto del array $bloqs
	private function replaceBloqs()
	{
		$tmpblq = $this->page->getBloqs();
		//Recorremos el array guardando la clave en la variable $tag
		foreach( $tmpblq as $clave => $template )
		{
			$templateContent = file_get_contents($tmpblq);
			$newContent = str_replace(G_SEPTAGI.$clave.G_SEPTAGF, $templateContent, $this->page->getContent());
			$this->page->setContent($newContent);
		}
	}

	//Reemplza los Tags de la página
 	private function replaceTags()
    {
		//Obtenemos los tags
		$tags = $this->page->getTags();
		//Recorremos el array guardando la clave en la variable $tag
		foreach( $tags as $clave=>$data )
		{
			if( is_array($data) )
			{
				if( $data[0] == G_TSQL )
					//Si se trata de datos en la BD
					$this->replaceDBTags($clave, $data[1]);
				elseif( $data[0] == G_TDATA )
					//Si se trata de datos en caché
					$this->replaceDataTags($clave, $data[1]);
				elseif( $data[0] == G_TARR )
					//Si se trata de datos en caché
					$this->replaceArrayTags($clave, $data[1]);
			}else{
				//Reemplaza el contenido del Tag
				$newContent = str_replace(G_SEPTAGI.$clave.G_SEPTAGF,$data,$this->page->getContent());
				//Actualiza el contenido
				$this->page->setContent($newContent);
			}
		}
	}

	//Reemplaza el contenido de la página con los datos del array
	private function replaceArrayTags($tag, $data)
	{
		$pageContent = $this->page->getContent();
  		//Recorremos todos los valores posibles recogidos en la caché
		foreach ($data as $key => $value)
    		$pageContent = str_replace(G_SEPTAGI.$key.G_SEPTAGF, $value, $pageContent);
		//Actualiza el contenido de la página
		$this->page->setContent($pageContent);
	}

 	//Reemplaza el contenido de la página con los datos de la consulta $cacheId
	private function replaceDBTags($tag, $cacheId)
	{
		$block = '';
		//Determinamos los tags a reemplazar
    	$blockOld = $this->page->getBlock($tag);
  		//Recorremos todos los valores posibles recogidos en la caché
    	while ($dbtag = cls_framework::getObject(G_IDXDB)->resultsFromCache($cacheId))
    	{
    	  	$blockNew = $blockOld;
        	//Se crea un bloque de contenido nuevo por cada registro recuperado
        	foreach ($dbtag as $ntag => $data){
				//Determinanos si hay que aplicar algún formato
				//Fecha - Hora
				if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF) == true )
					$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF, FmtFechaHora($data), $blockNew);
				else
					//Fecha
					if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_D.G_SEPTAGF) == true )
						$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF, FmtFecha($data), $blockNew);
						//Hora
					else
						if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_T.G_SEPTAGF) == true )
							$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_T.G_SEPTAGF, FmtHora($data), $blockNew);
						else
							//Enteros
							if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_I.G_SEPTAGF) == true )
								$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_I.G_SEPTAGF, FmtEnteros($data), $blockNew);
							else
								//Dobles 2 decimales
								if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_F.G_SEPTAGF) == true )
									$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_F.G_SEPTAGF, Fmt2Decs($data), $blockNew);
								else
									$blockNew = str_replace(G_SEPTAGI.$ntag.G_SEPTAGF, $data, $blockNew);
			}
			$block .= $blockNew;
		}
		$pageContent = $this->page->getContent();
		//Elimina los separadores de contenido en la plantilla
		$newContent = str_replace(G_TAGBEGIN.$tag.G_TAGCLOSE.$blockOld.G_TAGEND.$tag.G_TAGCLOSE, $block, $pageContent);
		//Actualiza el contenido de la página
		$this->page->setContent($newContent);
	}

    //Reemplaza el contenido de la página con los datos de la caché
    private function replaceDataTags($tag, $cacheId)
    {
		$block = $this->page->getBlock($tag);
		$blockOld = $block;
  		//Recorremos todos los valores posibles recogidos en la caché
		while ($dbtag = cls_framework::getObject(G_IDXDB)->dataFromCache($cacheId) )
		{
        	//Se crea un bloque de contenido nuevo por cada registro recuperado
			foreach ($dbtag as $ntag => $data)
    		{
    			//Determinanos si hay que aplicar algún formato
				//Fecha - Hora
				$blockNew = $blockOld;
				if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF) == true )
					$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF, FmtFechaHora($data), $blockNew);
				else
					//Fecha
					if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_D.G_SEPTAGF) == true )
						$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_DT.G_SEPTAGF, FmtFecha($data), $blockNew);
					else
						//Hora
						if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_T.G_SEPTAGF) == true )
							$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_T.G_SEPTAGF, FmtHora($data), $blockNew);
						else
							//Enteros
							if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_I.G_SEPTAGF) == true )
								$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_I.G_SEPTAGF, FmtEnteros($data), $blockNew);
							else
								//Dobles 2 decimales
								if ( stristr($blockNew, G_SEPTAGI.$ntag.G_TFMT_F.G_SEPTAGF) == true )
									$blockNew = str_replace(G_SEPTAGI.$ntag.G_TFMT_F.G_SEPTAGF, Fmt2Decs($data), $blockNew);
								else
									$blockNew = str_replace(G_SEPTAGI.$ntag.G_SEPTAGF, $data, $blockNew);
			}
			$block .= $blockNew;
		}
		$pageContent = $this->page->getContent();
		//Elimina los separadores de contenido en la plantilla
		$newContent = str_replace($blockOld, $block, $pageContent);
		//Actualiza el contenido de la página
		$this->page->setContent($newContent);
    }

	//Asigna el titulo de la página
	public function setTitle($title)
    {
    	if ( strlen($title) == 0 )
	 		$title = G_EMPRESA;
	 	else
	 		$title = $title.' - '.G_EMPRESA;
		$newContent = str_replace('<title>', '<title>'.$title,$this->page->getContent());
		$this->page->setContent($newContent);
	}

	//Remplaza los Tags Standar
	public function setStTags()
    {
		//Nombre Empresa {G_EMPRESA}
		$newContent = str_replace('{G_EMPRESA}',G_EMPRESA,$this->page->getContent());
		$this->page->setContent($newContent);
		//Inicio de Cabecera de Contenido {G_CabeceraIni}
		$newContent = str_replace('{G_CabeceraIni}',CabeceraIni(),$this->page->getContent());
		$this->page->setContent($newContent);
		//Fin de Cabecera de Contenido {G_CabeceraFin}
		$newContent = str_replace('{G_CabeceraFin}',CabeceraFin(),$this->page->getContent());
		$this->page->setContent($newContent);
		//Contenido Standar 1 {G_ContenidoStnd1}
		$newContent = str_replace('{G_ContenidoStnd1}',ContenidoStandar1(),$this->page->getContent());
		$this->page->setContent($newContent);

		//Ruta de Imágenes {imgpath}
		if ( $_SERVER['PHP_SELF'] == G_INDEX )
			$newContent = str_replace('{imgpath}', G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_SKINS.G_SKINACTUAL.G_BARRA.G_IMG,$this->page->getContent());
		else
			$newContent = str_replace('{imgpath}', G_BARRAPADRE.G_SKINS.G_SKINACTUAL.G_BARRA.G_IMG,$this->page->getContent());
		$this->page->setContent($newContent);

		//Ruta de Descargas {downpath}
		if ( $_SERVER['PHP_SELF'] == G_INDEX )
			$newContent = str_replace('{downpath}', G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_DOWN,$this->page->getContent());
		else
			$newContent = str_replace('{downpath}', G_BARRAPADRE.G_DOWN,$this->page->getContent());
		$this->page->setContent($newContent);
	}

	private function setHeaderSt($pathtpl)
	{
		$cad = "<!doctype html> \n";
		$cad .= "<html lang='es'>\n";
		$cad .= "<head>\n";
		$cad .= "<meta charset='utf-8' />\n";
		$cad .= "<meta charset='viewport' content='width=device-width, initial-scale=1, maximum-scale=1' />\n";
		$cad .= "<meta name='description' content='".G_METADESCR."' />\n";
		$cad .= "<title></title>\n";
		$cad .= "<link rel='stylesheet' type='text/css' href='".$pathtpl."css/normalize.css' />\n";
		$cad .= "<link rel='stylesheet' type='text/css' href='".$pathtpl."css/styles.css' />\n";
	 	$cad .= "<link rel='shortcut icon' href='{imgpath}favicon.ico' />\n";
		$cad .= "</head>\n";
		$cad .= "<body>\n";
		return $cad;

		// 03.01.2013 - Cabecera antigua
		/* $cad = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>\n";
		$cad .= "<html xmlns='http://www.w3.org/1999/xhtml' lang='es-ES'>\n";
		$cad .= "<head>\n";
		$cad .= "<meta name='title' content='".G_METATITLE."' />\n";
		$cad .= "<meta name='description' content='".G_METADESCR."' />\n";
		$cad .= "<meta name='keywords' content='".G_METAKEYWORDS."' />\n";
		$cad .= "<meta name='author' content='".G_DESEMPRESA."' />\n";
		$cad .= "<meta name='robots' content='all' />\n";
		$cad .= "<meta http-equiv='Content-Type' content='ISO-8859-1' />\n";
		$cad .= "<meta http-equiv='Expires' content='0' />\n";
		$cad .= "<meta http-equiv='Last-Modified' content='Mon, 11 Dec 2005 09:12:09 GMT' />\n";
		$cad .= "<meta http-equiv='Cache-Control' content='no-cache, must-revalidate' />\n";
		$cad .= "<meta http-equiv='Pragma' content='no-cache' />\n";
		$cad .= "<link rel='stylesheet' type='text/css' href='".$pathtpl."css/styles.css' />\n";
	 	$cad .= "<link rel='shortcut icon' href='{imgpath}favicon.ico' />\n";
		$cad .= "<title></title>\n";
		$cad .= "</head>\n";
		$cad .= "<body>\n";
		return $cad; */
    }


	//Genera el pie standar de un página
	private function setFooterSt()
	{
 		$cad = "</body>\n</html>";
		return $cad;
    }

	//Convierte en un array de datos todos los Tags
	public function dataToTags($data, $prefix)
	{
		foreach( $data as $key => $content )
			$this->page->addTag($key.$prefix, $content);
    }

}

?>