<?php

namespace aorm\sql;

class update extends \aorm\sql{

	private $_update = array(
		'UPDATE' => '',
		'SET' => array (),
		'WHERE' => array (),
		'RETURNING' => array()
	);
		
	public 
	function update($model){
		$this->model = $model ;
		$this->_statement = "UPDATE" ;

		return $this ;
	}
	
}