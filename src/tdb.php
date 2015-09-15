<?php

namespace aorm;

abstract
class tdb {

	static $connect = null;
	static $table = null;

	public
	function __construct(){
		
	}

	protected
	function getDatos(){

	}

	function test(){
		var_dump(aorm::getConnect(static::$connect));
	}
}