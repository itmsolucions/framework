<?php

//Clase principal del Framework//
//*****************************//

class cls_framework {

	//Atributos Basicos de la clase//
	//-----------------------------//
	private static $objects = array();		 					//Colección de objetos
	private static $settings = array();							//Colección de ajustes
	private static $frameworkName = G_VERSION;					//Nombre y versión del Framework
	private static $instance;									//Instancia del registro

  	//Operaciones de la clase//
	//-----------------------//

	//Constructor privado para evitar que se creen directamente
	private function __construct()
	{

	}

 	//Utilizamos el método singleton para acceder a los objetos. Si no existe se crea, si existe se devuelve el ya creado
	public static function singleton()
	{
		if( !isset(self::$instance) )
		{
			$obj = __CLASS__;
        	self::$instance = new $obj;
		}
		return self::$instance;
	}

	//Impedir la clonación de los objetos y lanzamos el error G_NOCLONE si se intenta
	public function __clone()
	{
		trigger_error(G_NOCLONE, E_USER_ERROR);
    }

	//Obtiene un objeto del registro
	public static function getObject($key)
	{
		if( is_object(self::$objects[$key]) )
		{
			return self::$objects[$key];
		}
	}

 	//Almacena los ajustes en el registro
	public function storeSetting($data, $key)
	{
		self::$settings[$key] = $data;
    }

	//Obtiene un ajuste del registro
	public static function getSetting($key)
	{
		return self::$settings[$key];
	}

 	//Obtiene el nombre del framework
	public function getFrameworkName()
	{
		return self::$frameworkName;
	}

 	//Crea un objeto database y un objeto template
	public function storeCoreObjects()
	{
		$this->storeObject(G_CLSDB, G_IDXDB);
		$this->storeObject(G_CLSTEMPLATE, G_IDXTEMPLATE);
	}

	//Almacena un objeto en el registro
	public function storeObject($object, $key)
	{
		if ( $_SERVER['PHP_SELF'] == G_INDEX )
			require_once(G_OBJS.$object.G_EXT);
		else
			require_once(G_BARRAPADRE.G_BARRAPADRE.G_BARRAPADRE.G_OBJS.$object.G_EXT);
		self::$objects[$key] = new $object(self::$instance);
	}

}

?>