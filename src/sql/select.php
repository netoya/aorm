<?php

namespace aorm\sql;

class select extends \aorm\sql{
	
	private $_select = array(
		"SELECT" => array(),
		"JOINS" => array(),
		"WHERE" => array(),
		"GROUP BY" => array(),
		"ORDER BY" => array(),
		"LIMIT" => array(),
		"OFFSET" => array()
	) ;

	public 
	function __construct(){
		$args = func_get_args() ;
		$this->_select["SELECT"] = $args ;

		return $this ;
	}

	public 
	function group(){
		$args = func_get_args() ;
		$model = $this->model ;

		$this->_select["GROUP BY"] = $args ;

		return $this ;
	}

	public 
	function order(){
		$args = func_get_args() ;
		$model = $this->model ;

		$this->_select["ORDER BY"] = $args ; ;

		return $this ;
	}
	
	public 
	function limit($limit, $offset = 0){
		$this->_select["LIMIT"] = intval($limit) ;
		$this->_select["OFFSET"] = intval($offset) ;
		
		return $this ;
	}
	
	public 
	function join ( $class, $alias, $on, $type = "INNER" ){
		$model = $this->model ;
		if(substr($class, 0, 3) == 'sql')
			$join = "$type JOIN " .substr($class, 3, strlen($class)) . " $alias \n\tON(" ;
		else
			$join = "$type JOIN " .$class::$table_name . " $alias \n\tON(" ;
		$str = gettype($on) == 'string' ? $on : $on[0] ;

		if(gettype($on) == 'array'){
			//convierte [":persona_id = ?", "p.id"] en ":persona_id = p.id"
			for($i = 1; $i < count($on) ; $i++){
				$str = preg_replace('/\?/', $on[$i] , $str, 1) ;
			}
		}
		$join .= $str .')' ;

		array_push( $this->_select['JOINS'], $join) ;

		return $this ;
	}
	
	public 
	function leftJoin($class, $alias, $on = array()){
		return $this->join($class, $alias, $on, "LEFT") ;
	}
	
	public 
	function rightJoin($class, $alias, $on = array()){
		return $this->join($class, $alias, $on, "RIGHT") ;
	}

	public 
	function build(){

		foreach($this->_select as $key => $value){
			$cmd = "" ;
			if(count($this->_select[$key]) > 0){
				
				if($key == "SELECT"){
					$model = $this->model ;
					if($this->_select[$key][0] == "*"){
						$this->_select[$key] = $model::$attributes ;

						for($i=0; $i < count($this->_select[$key]) ; $i++){
							$this->_select[$key][$i] = $this->alias .".". $this->_select[$key][$i] ;
						}

					}
					$from = $this->model ? ("\nFROM " .$this->from ." as " . $this->alias) : '' ;
					$cmd = "\nSELECT\n\t" .implode(", ", $this->_select[$key]) . "$from\n" ;

				}
				
				elseif ($key == "JOINS"){
					$cmd = implode( "", $this->_select[$key] );
				}
				
				elseif ($key == "WHERE"){
					$cmd = "\n$key " . implode( "\n", $this->_select[$key] ) ;
				}
				
				elseif ($key == "GROUP BY" || $key == "ORDER BY"){
					$cmd = "\n$key " . implode( ', ', $this->_select[$key] ) ;
				}
				
				elseif ($key == "LIMIT" || $key == "OFFSET"){
					$cmd = "\n$key " . $this->_select[$key] ;
				}

				$this->_select[$key] = array() ;
				$this->query .= $cmd ;
			}
		}
		
		return $this->query ;
	}
		
	public 
	function where(){
		$model = $this->model ;
		$args = func_get_args() ;
		$str = $args[0] ;
		$begin = 1 ;

		if($args[0] == 'and' || $args[0] == 'or'){
			$str = " ".$args[0] . ' ' .$args[1] ;
			$begin = 2 ;
		}

		//convierte ["pos.persona_id = ?", "p.id"] a "pos.persona_id = p.id"
		for($i = $begin; $i < count($args) ; $i++){
			if(gettype($args[$i]) == 'array') $args[$i] = implode(',', $args[$i]) ;
			$str = preg_replace('/\?/', $args[$i] , $str, 1) ;
		}

		array_push( $this->_select['WHERE'], $str) ;
		
		return $this ;
	}
}