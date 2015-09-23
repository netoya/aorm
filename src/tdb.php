<?php

namespace aorm;
abstract
class tdb {

	static $_connect = null;
	static $_table = null;
	static $_where = null;
	static $_columns = [];

	protected $id = null;

	protected $data_current = [];
	protected $data_new = [];

	public
	function __construct(){

	}

	static
	function getInstance($data){
		$select = static::sql();

		foreach ($data as $where) {
			$select->where($where);
		}

		$select->limit(1);
		
		$obj = $select->run(get_called_class());
		if(empty($obj)){
			return null;
		}


		return $obj[0];
	}

	static
	function getList($data){
		$select = static::sql();

		foreach ($data as $where) {
			$select->where($where);
		}

		$obj = $select->run(get_called_class());

		return $obj;
	}

	public
	function __get($name){
		if($name == 'id'){
			return $this->id;
		}

		if(isset($this->data_new[$name])){
			return $this->data_new[$name];
		}

		if(isset($this->data_current[$name])){
			return $this->data_current[$name];
		}

		$trace = debug_backtrace();
    trigger_error(
        'Undefined property via __get(): ' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
	}

	public
	function __set($name, $value){
		
		if(!$this->columnExists($name)){
			$tables = array();
			foreach ((array)static::$_table as $table) {
				$tables[] = $table;
			}

			throw new \Exception($name . " column not exists in " . implode('/',$tables), 1);
		}

		if(!empty($this->id)){ // When get by db
			if(!isset($this->data_current[$name])){ // add in current data if not exists
				$this->data_current[$name] = $value;	
			} else {	// else add in changed data
				$this->data_new[$name] = $value;
			}
			
		} else { // if new instance add changed data
			$this->data_new[$name] = $value;
		}


	}

	static 
	function columns(){
		$tables  = (array)static::$_table;
		$conn 	= static::connect();

		if(empty(static::$_columns)){

			$columns = array();
			foreach($tables as $table){
				$columns = array_merge($columns, $conn->getTableColumns($table));
			}
				
			static::$_columns = $columns;
		}

		return static::$_columns;
	}

	protected
	function columnExists($name){
		$columns = static::columns();

		if(key_exists($name, static::$_columns)){
			return true;
		}

		return false;
	}

	public
	function modified(){
		if(!empty($this->data_new)){
			return true;
		}

		if(empty($this->id)){
			return true;
		}

		return false;
	}

	public
	function reset(){
		$this->data_new = [];
	}

	public
	function save(){

	}

	static
	function sql(){

		$table = static::$_table;
		
		$select = new \aorm\sql\select(static::connect());
		$select->addColumn('*');
		if(is_array($table)){
			foreach ($table as $alias => $name) {
				if(is_numeric($alias)){
					$select->from($name);
				} else {
					$select->from($name, $alias);
				}
			}
		} elseif(is_string($table)){
			$select->from($table);
		} else {
			throw new \Exception("static::$_table expected a string but was " . gettype($table) . ".", 1);
		}

		$where = static::$_where;
		if(!empty($where) ){
			$select->where($where);
		}

		return $select;
	}

	static
	function connect(){	
		return aorm::getConnect(static::$_connect);
	}

	function test(){
		$this->connect();
		$dbc = $connect->getDbc();
		$columns = $connect->getTableColumns(static::$table);
		echo '<pre>';
		print_r($columns);
		echo '</pre>';
	}
}