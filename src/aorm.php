<?php

namespace aorm;

use aorm\connect as connect;

class aorm{

	static $instance;
	static $connect;

	protected
	function __construct(){

	}

	public
	function getInstance(){
		if(empty($instance)){
			static::$instance = new static();
		}
		
		return static::$instance;
	}

	static 
	function getConnect($name){
		if(empty(static::$connect[$name])){
			throw new Exception("La conexion [" .$name . "] no existe", 1);
			
		}
		return static::$connect[$name];
	}

	static
	function addConnect($name, connect $connect){
		
		static::$connect[$name] = $connect;

		return $connect;
	}


}