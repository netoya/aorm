<?php 
namespace aorm\connect;

class pgsql extends \aorm\connect{
	
	protected
	function connectDB(){
		$settings = $this->settings;
		$params['host']    	= 'host='    . $settings['host'];
		$params['port']    	= 'port='    . $settings['port'];
		$params['dbname']   = 'dbname='    . $settings['dbname'];
		$params['charset']	= 'options=\'--client_encoding=' . strtoupper($settings['charset']). '\'';

		$user    = $settings['user'];
		$pass    = $settings['pass'];

		$PDO_DSN = 'pgsql:' . implode(';', $params);

		$pdo = new PDO($PDO_DSN, $user, $pass );


		if(!empty($this->settings['scheme'])){
		    $pdo->exec('SET search_path TO ' . $settings['scheme']);
		}

		$this->dbc = $pdo;
		return $dbc;
	}

	public
	function geTableColumns($table_name){
		$query = " SELECT attrelid::regclass, attnum, attname " .
				 " FROM   pg_attribute " .
				 " WHERE  attrelid = '?'::regclass " .
				 " AND    attnum > 0 " .
				 " AND    NOT attisdropped " .
				 " ORDER  BY attnum ";

		$params = array();
		$params[] = $table_name;

		$rs = $this->execute($query, $params);

		while ($obj = $result->fetchObject($class)) {
		}
		
		return $rs;
	} 

	public
	function execute($query, $params){
		$query = str_ireplace("%", "%%", $query);
		$query = str_ireplace("?", "%d", $query);

		$params = (array) $params;
		$params = array_merge(array($query), $params);
		$query = call_user_func_array('sprintf', $params);

		$dbc = $this->getDBC();
		$result = $dbc->query($query);

		return $result;
	}



}