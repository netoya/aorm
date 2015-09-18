<?php
namespace aorm;

 
class sql {

	protected $connect ;
	
	private $query = "" ;
	function __construct(){

	}

	public
	function setConnect(\aorm\connect $connect){
		$this->connect = $connect;
	}
	
	public
	function setQuery($query){

	}

	public
	function addParam($params, $value){

	}

	public
	function run(){
		
	}

}

