<?php

//Clase para tratar las propiedades de las paginas//
//************************************************//

class cls_pageadmin {

 	//Atributos Basicos de la clase//
	//-----------------------------//

	//Posible opciones a implementar en un futuro
	private $css = array();
	private $js = array();
	private $bodyTag = '';
	private $bodyTagInsert = '';

 	//Nos servirá en un futuro para loguear usuarios
	private $authorised = true;
	private $password = '';

 	//Elementos de la página
	private $content = '';										//Contenido de la pagina
	private $tags = array();									//Array de Tags
	private $postParseTags = array();							//Array de Tags Parseados
	private $bloqs = array();									//Array de Bloques de Texto

	//Operaciones de la clase//
	//-----------------------//

	//Constructor de la clase
	function __construct()
	{

	}

	//Obtiene el fragmento de la página envuelta entre el tag $tag ( <!-- START tag --> bloque <!-- END tag --> )
	//y lo almacena en el array $contenido
    public function getBlock($tag)
	{
    	preg_match ("#".G_TAGBEGIN.$tag.G_TAGCLOSE."(.+?)".G_TAGEND.$tag.G_TAGCLOSE."#si",$this->content,$contenido);
		if ( count($contenido) > 0 ){
			//Y eliminamos la linea de Tags
			$contenido = str_replace(G_TAGBEGIN.$tag.G_TAGCLOSE, "", $contenido[0]);
			$contenido = str_replace(G_TAGEND.$tag.G_TAGCLOSE, "", $contenido);
		}
		return $contenido;
	}

    //Establece la variable password
	public function setPassword($password)
	{
		$this->password = $password;
	}

	//Establece la variable content
 	public function setContent($content)
	{
		$this->content = $content;
	}

 	//Devuelve la variable content
	public function getContent()
	{
		return $this->content;
	}

 	//Agrega un Tag en el array tags
	public function addTag($key, $data)
	{
		$this->tags[$key] = $data;
	}

  	//Devuelve el array de Tags
 	public function getTags()
	{
		return $this->tags;
	}

 	//Agrega un Tag en el array postParseTags
 	public function addPPTag($key, $data)
	{
		$this->postParseTags[$key] = $data;
    }

   	//Devuelve el array de Tags (Los que ya se han parseado)
	public function getPPTags()
	{
		return $this->postParseTags;
	}

	//Añade un bloque en la página
	public function addTemplateBloq($tag, $blq)
	{
		$this->bloqs[$tag] = $blq;
	}

    //Obtener los bloques de la plantilla que se introducirán en la página. Devuelve un array con todos los tags
	//de la página y con los nombres de las plantillas
	public function getBloqs()
	{
		return $this->bloqs;
	}
}

?>