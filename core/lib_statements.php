<?php

// Variables Generales de la Aplicación //
//**************************************//

//Datos Empresa Desarrolladora
define('G_NOMBREDES1','cronos'); 		        		        //Nombre del PC de Desarrollo 1
define('G_NOMBREDES2','itena.cenet.es');	   		    	    //Nombre del PC de Desarrollo 2
define('G_DESEMPRESA','ITM Solucions');         			    //Nombre de la Empresa
define('G_VERSION', 'Fw ITM Solucions  1.0');					//Versión del Framework

//Constantes
define('G_FWITM', true);										//Usaremos esta definición para evitar que los scripts se llamen desde fuera del framework
define('G_INDEX', '/index.php');								//Archivo inicial del Proyecto
define('G_EXT', '.php');										//Extensión utilizada en los archivo PHP
define('G_INTENTOS', '3');										//Intentos de validación en la entrada
define('G_SEPDEC', ','); 										//Separador Decimal
define('G_SEPMIL', '.'); 										//Separador Miles
define('G_SEPFEC', '/'); 										//Separador Fecha
define('G_BARRA', '/'); 										//Barra separadora de ficheros
define('G_DOSPUNTOS', '..'); 									//Dos punto
define('G_BARRAPADRE', '../'); 									//Barra separadora para directorio padre
define('G_TAMPAGINA','10');										//Cantidad de registros que muestra la paginación de resultados
define('G_FILELOG','logs/error.log');							//Archivo de logs
define('G_SQLLOG','logs/sql.log');								//Archivo de logs para instrucciones SQL
define('G_SERVIDORLOCAL','localhost');							//Host, nombre del servidor o IP del servidor Mysql.

//Formatos Fecha y Hora
define('G_ZONE','Europe/Madrid');								//Zona horaria
define('G_FHORA','H:i:s');			                        	//Formato Hora
define('G_FFECHA','d/m/Y');			                        	//Formato Fecha
define('G_FFECHAHORA','d/m/Y H:i:s');                           //Formato Fecha/Hora
define('G_FFECHAMY','Y-m-d');		                        	//Formato Fecha Mysql
define('G_FFECHAHORAMY','Y-m-d H:i:s');                         //Formato Fecha/Hora Mysql

//Tags
define('G_SEPTAGI', '{'); 										//Separador Inicial de Tags
define('G_SEPTAGF', '}'); 										//Separador Inicial de Tags
define('G_TAGBEGIN', '<!-- START '); 							//Constante par inicio de Tags
define('G_TAGEND', '<!-- END '); 								//Constante par fin de Tags
define('G_TAGCLOSE', ' -->'); 									//Cierra Tags
define('G_TSQL', 'SQL'); 										//Tipo de Datos SQL
define('G_TDATA', 'DATA'); 										//Tipo de Datos Datos
define('G_TARR', 'ARRAY');										//Tipo de Datos Array
define('G_TFMT_DT', ':dt'); 									//Formato de Tag Fecha-Hora según G_FFECHAHORA
define('G_TFMT_D',  ':d'); 										//Formato de Tag Fecha según G_FFECHA
define('G_TFMT_T',  ':t'); 										//Formato de Tag Hora según G_FHORA
define('G_TFMT_I',  ':i'); 										//Formato de Tag Enteros con G_SEPDEC y G_SEPMIL como separadores
define('G_TFMT_F',  ':f'); 										//Formato de Tag Dobles a dos decimales G_SEPDEC y G_SEPMIL como separadores

//Rutas
define('G_OBJS', 'core/objects/');								//Carpeta de objetos del Core
define('G_PROJECTS', 'projects/');								//Carpeta de proyectos
define('G_SKINS', 'skins/');									//Carpeta de skins
define('G_CONTROLLERS', 'contrls/');							//Carpeta con controladores del Skin

//Clases y Librerías Genéricas
define('G_CLSDB', 'cls_db');									//Nombre de la clases para la base de datos
define('G_CLSTEMPLATE', 'cls_template');						//Nombre de la clase para las plantillas
define('G_CLSPAGEADMIN', 'cls_pageadmin');						//Nombre de la clase para gestionar las páginas
define('G_LIBPROJECT', 'lib_project.php');						//Nombre de la libreria específica

//Indices
define('G_IDXDB', 'idx_db');									//Indice para la base de datos
define('G_IDXSKIN', 'idx_skin');								//Indice para el skin
define('G_IDXTEMPLATE', 'idx_template');						//Indice para la plantilla

//Gestión de Errores
define('G_GESTERRS','APL_ERROR');								//Texto para diferencias los errores de las excepciones
define('G_ERUSERGUEST','(Invitado)');							//Usuario invitado
define('G_SEPTXTERR',' - ');									//Separador para mostrar los errores
define('G_SEPPARERR','#');										//Separador para los parámetros de errores
define('G_EREXCCOD','Excepción de Código');						//Titulo para los diferentes tipos de errores:
define('G_ERMSJ','Mensaje');									//Descripción Tipo Error Mensaje
define('G_ERERROR','Error');									//Descripción Tipo Error Error
define('G_ERADV','Advertencia');								//Descripción Tipo Error Adventencia
define('G_ERINTERP','Error de Interprete');						//Descripción Tipo Error Error de Interprete
define('G_ERAVISO','Aviso');									//Descripción Tipo Error Aviso
define('G_ERNUCLEO','Error de Núcleo');							//Descripción Tipo Error Error de Núcleo
define('G_ERADVNUC','Advertencia de Núcleo');					//Descripción Tipo Error Advertencia de Núcleo
define('G_ERCOMP','Error de Compilación');						//Descripción Tipo Error Error de Compilación
define('G_ERADVCOMP','Advertencia de Compilación');				//Descripción Tipo Error Advertencia de Compilación
define('G_ERUSER','Error de Usuario');							//Descripción Tipo Error Error de Usuario
define('G_ERADVUSER','Error de Usuario');						//Descripción Tipo Error Error de Usuario

//Mensajes Generales
define('G_NOCLONE', 'La clonación del registro no está permitida.');
define('G_ERRBDCONEX', 'Error al establecer la conexión con la BD.');
define('G_ERRBDCONSCACHE', 'Error al ejecutar y cachear a la consulta: ');
define('G_ERRBDCONS', 'Error al ejecutar la consulta: ');
define('G_ERRCLSTEMP', 'Solo se pueden realizar llamadas desde el propio framework: '.G_VERSION);
define('G_ERRELOADTEMPLATE', 'Error al cargar la plantilla:');
define('G_ERRMSG','Se ha producido un error en la función ');

?>
