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
		$connect = aorm::getConnect(static::$connect);
		$dbc = $connect->getDbc();
		$columns = $connect->getTableColumns(static::$table);
		echo '<pre>';
		print_r($columns);
		echo '</pre>';
	}
}