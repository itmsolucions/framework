<?php
//Iniciamos la sesión en el servidor
session_cache_limiter('nocache');
session_start();

//Llamamos a las librerías y definimos la plantilla
$pltpage = 'tpl_index.html';
$titlepage = '';
require_once('./core/lib_libraries.php');
require_once(G_PROJECTS.'itmsolucions'.G_BARRA.G_CONTROLLERS.G_LIBPROJECT);

//Función de autoload que se utiliza para incluir una libreria en caso de error antes de que se genere la excepción (PHP5)
//Al invocar a esta función el motor de scripting está dando una última oportunidad de cargar la clase antes que PHP falle con un error.
function __autoload($class_name)
{
    require_once(G_OBJS.$class_name.G_EXT);
}

//Creamos el obtejo de la clase, lo instanciamos con singleton para restringir la creación a una sola instancia que será compartida y usada por todas las páginas.
//Después generamos los objetos y establecemos el Skin por defecto y su librería(También se puede almacenar en la BD)
$pagina = cls_framework::singleton($_SERVER['PHP_SELF']);
$pagina->storeCoreObjects();
$pagina->storeSetting(G_SKINACTUAL, G_IDXSKIN);
require_once(G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_SKINS.G_SKINACTUAL.G_SKINSFUNC);

//Contenido Web
//---------------------------------------------------------------------------------------------------------------------
//Abrimos una conexión con la BD y cacheamos las consultas necesarias con un ID de cache y asignamos los datos a un Tag
$pagina->getObject(G_IDXDB)->newConnection(G_SERVIDOR, G_USUARIO, G_CLAVE, G_BDATOS);
$tag = 'configuracion';
$id_conf = $pagina->getObject(G_IDXDB)->cacheQuery($pagina->getObject(G_IDXDB)->selectStatement($tag));
$pagina->getObject(G_IDXTEMPLATE)->getPage()->addTag($tag, array(G_TSQL, $id_conf));

//----------------------------------
//Inicio PHP Específico de la Página
//----------------------------------


//----------------------------------
//Fin PHP Específico de la Página
//----------------------------------

//Llenamos el objeto página a partir de la plantilla y establecemos el titulo
$pagina->getObject(G_IDXTEMPLATE)->buildFromTemplate($pltpage, $titlepage);
//Parseamos los tags
$pagina->getObject(G_IDXTEMPLATE)->parseOutput($titlepage);
//Y mostramos en pantalla
echo $pagina->getObject(G_IDXTEMPLATE)->getPage()->getContent();
//---------------------------------------------------------------------------------------------------------------------

exit();
?>