<?php

namespace aorm;

use aorm\connect as connect;

class aorm{

	static $instance;

	public
	function __construct(){

	}

	static 
	function getConnect(){
		return static::$connect;
	}

	static
	function setConnect(connect $connect){

		static::$connect = $connect;

		return static::$instance;
	}


}