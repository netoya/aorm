<?php 
namespace aorm\connect;

class pgsql extends \aorm\connect{

	static $defaults = array('port' => '5432');
	
	protected
	function connectDB(){
		$settings = $this->settings;
		$params['host']    	= 'host='    . $settings['host'];
		$params['port']    	= 'port='    . $settings['port'];
		$params['dbname']   = 'dbname='  . $settings['dbname'];

		if(!empty($settings['charset'])){
			$params['charset']	= 'options=\'--client_encoding=' . strtoupper($settings['charset']). '\'';
		}

		$user    = $settings['user'];
		$pass    = $settings['pass'];

		$PDO_DSN = 'pgsql:' . implode(';', $params);

		$pdo = new \PDO($PDO_DSN, $user, $pass );


		if(!empty($this->settings['scheme'])){
		    $pdo->exec('SET search_path TO ' . $settings['scheme']);
		}

		$this->dbc = $pdo;
		return $pdo;
	}

	public
	function getTableColumns($table_name){
/*
		$query = " SELECT attrelid::regclass, attnum, attname " .
				 " FROM   pg_attribute " .
				 " WHERE  attrelid = '?'::regclass " .
				 " AND    attnum > 0 " .
				 " AND    NOT attisdropped " .
				 " ORDER  BY attnum ";
*/
		$schema = 'public';
		$table_name = explode('.', $table_name);
		$table 	= array_pop($table_name);

		if(!empty($table_name)){
			$schema = array_pop($table_name);
		}

		$query =  "SELECT f.attnum AS number, 
											f.attname AS name, 
											f.attnotnull AS notnull,
									    pg_catalog.format_type(f.atttypid,f.atttypmod) AS type,
									    CASE
										WHEN p.contype = 'p' THEN 't'
										ELSE 'f'
									    END AS primarykey,
									    CASE
										WHEN p.contype = 'u' THEN 't'
										ELSE 'f'
									    END AS uniquekey,
									    CASE
										WHEN p.contype = 'f' THEN gn.nspname || '.' || g.relname
									    END AS foreignkey,
									    CASE
										WHEN f.atthasdef = 't' THEN d.adsrc
									    END AS default
									FROM pg_attribute f
									    JOIN pg_class c ON c.oid = f.attrelid
									    JOIN pg_type t ON t.oid = f.atttypid
									    LEFT JOIN pg_attrdef d ON d.adrelid = c.oid AND d.adnum = f.attnum
									    LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
									    LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY (p.conkey)
									    LEFT JOIN pg_class AS g ON p.confrelid = g.oid
									    LEFT JOIN pg_catalog.pg_namespace gn ON gn.oid = g.relnamespace 
									WHERE c.relkind = 'r'::char
									    AND n.nspname = '" . $schema . "'
									    AND c.relname = '" . $table . "'
									    AND f.attnum > 0 ORDER BY number asc, foreignkey desc";


		$params = array();
		$params[] = $table_name;

		$rs = $this->execute($query, $params);

		$columns = [];
		while ($obj = $rs->fetchObject()) {
			$columns[] = $obj;
		}
		
		return $columns;
	} 

	public
	function execute($query, $params){
		$query = str_ireplace("%", "%%", $query);
		$query = str_ireplace("?", "%s", $query);

		$params = (array) $params;
		$params = array_merge(array($query), $params);
		$query = call_user_func_array('sprintf', $params);

		$dbc = $this->getDBC();
		$result = $dbc->query($query);

		return $result;
	}



}