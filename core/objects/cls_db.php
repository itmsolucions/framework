<?php

//Clase para la conexión a la base de datos//
//*****************************************//

class cls_db {

	//Atributos Basicos de la clase//
	//-----------------------------//
	private $connections = array();								//Permite múltiples conexiones a la base de datos.  Es posible que nunca se utilice, pero por ofrecer la opción
	private $activeConnection = 0;								//Conexión activa a la base de datos. Permite cambiar la conexión activa: setActiveConnection($id)
	private $queryCache = array();				  				//Consultas que se han ejecutado y que se guardan en caché para volver a ejecutarlas si fuese necesario
	private $dataCache = array();								//Los datos que se han recuperado, también se cachean para su posterior uso
	private $last; 												//Registro de la última consulta

	//Operaciones de la clase//
	//-----------------------//

	//Constructor de la clase
	public function __construct()
	{

	}

    //Cierra todas las conexiones a la base de datos
    public function __deconstruct()
    {
	    try
		{
			foreach( $this->connections as $connection )
				$connection->close();
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Crea una nueva conexión a base de datos
	public function newConnection($host, $user, $password, $database)
	{
		try
		{
			if ( gethostbyaddr($_SERVER['REMOTE_ADDR']) == G_NOMBREDES1 || gethostbyaddr($_SERVER['REMOTE_ADDR']) == G_NOMBREDES2 )
				$host=G_SERVIDORLOCAL;
			$this->connections[] = new mysqli($host, $user, $password, $database);
			$connection_id = count($this->connections) - 1;
			if( mysqli_connect_errno() )
				throw new Exception(G_ERRBDCONEX.$this->connections[$connection_id]->error);
			$this->connections[$connection_id]->query("SET NAMES 'utf8'");
			return $connection_id;
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Cierra la conexión activa
	public function closeConnection()
	{
	    try
		{
			$this->connections[$this->activeConnection]->close();
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Cambia la conexión activa donde $new es el identificador de la nueva conexión
    public function setActiveConnection(int $new)
	{
    	try
		{
			$this->activeConnection = $new;
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

  	//Consulta sin Cache//
	//------------------//

    //Ejecuta una consulta en la BD enlazada. Ejemplo: $objBD->executeQuery("select * from tabla;");
    public function executeQuery($sql)
    {
    	try
		{
			if( !$result = $this->connections[$this->activeConnection]->query($sql) )
				throw new Exception(G_ERRBDCONS.$this->connections[$this->activeConnection]->error);
			else{
				$this->regSql($sql);
				$this->last = $result;
				return $this->last;
			}
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
    }

	//Permite realizar multiples consultas separadas por ";" en la BD enlazada Ejemplo: $objBD->executeMultiQuery("Delete * from tabla; select * from tabla;");
	public function executeMultiQuery($sql)
	{
		try
		{
			$this->connections[$this->activeConnection]->multi_query($sql);
			$this->last = $this->connections[$this->activeConnection]->store_result();
			$this->regSql($sql);
			return $this->last;
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Libera el contenido de la consulta $this->consulta
    public function releaseQuery()
	{
		try
		{
		if ( $this->last->free_result() )
			throw new Exception(mysqli_errno($this->connections[$connection_id])." - ".mysqli_error($this->connections[$connection_id]));
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

    //Obtener las filas de la consulta ejecutada más recientemente en un arrary asociativo, con exclusión de las consultas cacheadas
	public function getRows()
	{
		try
		{
			return $this->last->fetch_array(MYSQLI_ASSOC);
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

 	//Devuelve el número de registros que tiene la consulta $this->consulta
 	public function numRows()
	{
		try
		{
			return $this->last->num_rows;
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

    //Obtiene el número de las filas afectadas en la última consulta realizada ( insert, update, delete etc)
	public function affectedRows()
	{
		try
		{
			return $this->$this->connections[$this->activeConnection]->affected_rows;
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Limpia los caracteres especiales de una cadena para usarla en una sentencia SQL
    public function cleanData($data)
    {
    	try
		{
			return $this->connections[$this->activeConnection]->real_escape_string($data);
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Ultimo Id autonumérico utilizado de la conexión actual. Ojo, tener en cuenta que es de la conexión en que se ha realizado el Insert.
	public function lastId()
	{
		try
		{
			if ( ! $this->last = $this->executeQuery('Select LAST_INSERT_ID();') )
				throw new Exception(mysqli_errno($this->connections[$connection_id])." - ".mysqli_error($this->connections[$connection_id]));
			else{
				$fila = $this->last->fetch_row();
				return $fila[0];
			}
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

    //Devuelve la consulta en un array asociativo
	public function resultsFromQuery()
	{
		try
		{
			return $this->last->fetch_array(MYSQLI_ASSOC);
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

  	//Consulta con Cache//
	//------------------//

    //Almacena una consulta en caché para su posterior uso y devuelve el identificador
    public function cacheQuery($sql)
    {
		try
		{
			if( !$result = $this->connections[$this->activeConnection]->query($sql) ){
				throw new Exception(G_ERRBDCONSCACHE.$this->connections[$this->activeConnection]->error);
				return -1;
			}else{
				$this->queryCache[] = $result;
				$this->regSql($sql);
				return count($this->queryCache) - 1;
	        }
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
    }

	//Obtiene el número de filas de la consulta de cache indicada en $cache_id
	public function numRowsFromCache($cache_id)
	{
		try
		{
			return $this->queryCache[$cache_id]->num_rows;
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

    //Devuelve la consulta de cache $cache_id en un array asociativo
	public function resultsFromCache($cache_id)
	{
		try
		{
			return $this->queryCache[$cache_id]->fetch_array(MYSQLI_ASSOC);
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

    //Guardar los datos en caché para su posterior uso
	public function cacheData($data)
    {
		try
		{
	        $this->dataCache[] = $data;
    	    return count($this->dataCache) - 1;
    	}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
    }

    //Obtiene datos de la caché
	public function dataFromCache($cache_id)
	{
		try
		{
			return $this->dataCache[$cache_id];
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//SQL//
	//---//

	//Genera una instrucción Select con todos los campos de la tabla $table
    public function selectStatement($table)
    {
		$cad = 'Select ';
		$fields = $this->executeQuery('Desc '.$table.';');
		while ($fila = $fields->fetch_row())
		    $cad .= $fila[0].', ';
   	    $cad = substr($cad, 0, -2);
   	    $cad .= " From $table;";
		return $cad;
	 }

	//Inserta registros
    public function insertRecords($table, $data)
    {
		try
		{
			$fields = '';
			$values = '';
 			//Rellena las variables con los campos y sus valores
			foreach ($data as $f => $v)
			{
				$fields .= "`$f`,";
				$values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : "'$v',";
    	    }
			//Quitamos la coma del final e insertamos ¿? 2 VECES <-- OJO
			$fields = substr($fields, 0, -1);
 			$values = substr($values, 0, -1);
			$insert = "INSERT INTO $table ({$fields}) VALUES({$values})";
	        $this->executeQuery($insert);
    	    return true;
    	}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
    }

	//Actualizar datos
	public function updateRecords($table, $changes, $conditions)
    {
    	try
    	{
			$cond = '';
			$update = 'UPDATE '.$table.' SET ';
    		foreach( $changes as $field => $value )
				$update .= "`" . $field . "`='{$value}',";
 			//Quitamos la coma del final
    	    $update = substr($update, 0, -1);
        	//Y agregamos la lista de condiciones
			foreach ($conditions as $f => $v)
				$cond .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? "$f=".$v." AND " : "$f='$v' AND";
			$cond = substr($cond, 0, -4);
        	if( $cond != '' )
				$update .= ' WHERE '.$cond.';';
 			$this->executeQuery($update);
			return true;
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Elimina un registro en la base de datos
    public function deleteRecords($table, $data, $limit='')
	{
		try
		{
			$limit = ( $limit == '' ) ? '' : ' LIMIT ' . $limit;
			$condition = '';
			//Creaamos la lista de condiciones
			foreach ($data as $f => $v)
				$condition .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? "'$f'=".$v."," : "'$f'='$v',";
			$condition = substr($condition, 0, -1);
			$delete = "DELETE FROM {$table} WHERE {$condition} {$limit}";
			$this->executeQuery($delete);
		}
	    catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
	}

	//Otras Funciones//
	//---------------//

    //Función que registra una cadena SQL generada
    private function regSql($sql)
    {
		try
		{
			if ( $_SERVER['PHP_SELF'] <> G_INDEX )
				$flog = G_BARRAPADRE.G_SQLLOG;
			else
				$flog = G_PROJECTS.G_PROJECTACTUAL.G_BARRA.G_SQLLOG;
			$f = fopen($flog,'a');
			$fecha = date(G_FFECHAHORA);
			fwrite($f,"[$fecha] - ".$_SERVER['PHP_SELF']." - $sql\r\n");
			fwrite($f,"\r\n");
			fclose($f);
		}
		catch (Exception $e)
		{
			throw new Exception(G_ERRMSG.__FUNCTION__);
		}
    }

}

?>