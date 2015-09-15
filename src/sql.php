<?php
namespace aorm;

 
class sql {

	private $_statement ;
	
	private $from = "" ;
	private $query = "" ;
	private $model = "" ;
	private $database_server = '' ;

	function __construct(){

	}
	
	public 
	function build(){}

	public 
	function run(){
		$model = $this->model ? $this->model : '' ;
		$connection_string = Server::getDefault() ;

		if ($model != '' && isset($model::$server)) {
			$this->database_server = $model::$server ;
			$connection_string = Server::getConnections($this->database_server) ;
		}

		$resource = pg_connect($connection_string) ;
		$result = array() ;

		if(!$resource){
			die("No se puede conectar a la base de datos del servidor " .$this->database_server) ;
			return $result ;
		}

		else {
			$this->build() ;
			error_log("\n" . $this->query ) ;
			$result = pg_query($resource, $this->query) ;
			$this->query = '' ;
			$result = pg_fetch_all($result) ;
			pg_close($resource) ;
		}

		return $result ;
	}

	public 
	function from ( $model, $alias ){
		$this->model = $model ;
		$this->from = $model::$table_name ;
		$this->alias = $alias ;

		return $this ;
	}

	public 
	function sql($sql) {
		$connection_string = Server::getDefault() ;

		$resource = pg_connect($connection_string) ;
		$result = array() ;

		if(!$resource){
			die("No se puede conectar a la base de datos del servidor " .$this->database_server) ;
			return $result ;
		}

		else {

			error_log("\n" . $sql ) ;
			$result = pg_query($resource, $sql) ;
			$result = pg_fetch_all($result) ;
			pg_close($resource) ;
		}

		return $result ;
	}
}

