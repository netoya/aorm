<?php

namespace aorm\sql;

class insert extends \aorm\sql{

	private $_insert = array(
		'INSERT INTO' => '',
		'COLUMNS' => array(),
		'VALUES' => array (),
		'RETURNING' => array()
	);
	
	public 
	function insert($model){
		$this->model = $model ;
		$table_name = $model::$table_name ;
		$this->_insert["INSERT INTO"] = $table_name ;
		$this->_statement = "INSERT" ;
		
		return $this ;
	}

	public 
	function build(){
		$statement = '' ;
		foreach($this->_insert as $key => $value){
			if(count($this->_insert[$key]) > 0){
				if($key == 'INSERT INTO'){
					$statement = $key .' ' .$this->_insert[$key] ;
				}
				elseif($key == 'COLUMNS'){
					$statement .= ' ( ' .implode(', ', $this->_insert['COLUMNS']) . ' )' ;
				}
				elseif($key == 'VALUES'){
					for($i = 0; $i < count($this->_insert['VALUES']); $i++){
						if(gettype($this->_insert['VALUES'][$i]) == 'string')
							$this->_insert['VALUES'][$i] = "'" .$this->_insert['VALUES'][$i] . "'" ;
					}
					$statement .= "\n" .$key ." ( " .implode(", ", $this->_insert[$key]) .") RETURNING (*)" ; 
				}
			}
		}
		$this->query = $statement ;
		
		return $this->query ;
	}
	
	public 
	function columns(){
		$model = $this->model ;
		$columns = func_get_args() ;
		if ($columns[0] == '*'){
			$columns = $model::$attributes ;
		}
		$this->_insert["COLUMNS"] = $columns ;
		
		return $this ;
	}
	
	public 
	function values(){
		$values = func_get_args() ;
		if(gettype($values[0]) == 'array')
			$values = $values[0] ;

		$columns = $this->_insert["COLUMNS"] ;
		$valores = array() ;
		if(count($columns) == 0){
			foreach($values as $column => $value){
				array_push($columns, $column) ;
				array_push($this->_insert["VALUES"], $value) ;
			}
			$this->_insert['COLUMNS'] = $columns ;
		}
		else
			$this->_insert["VALUES"] = $values ;
			
		return $this ;
	}
	
}