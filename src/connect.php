<?php

namespace aorm;

abstract class connect{

	static $instance;
	static $defaults = array();
	protected $dbc;
	protected $settings;

	public
	function __construct($settings = array()){
        $this->settings = array_merge(static::$defaults, $settings);
	}

	public
	function getDBC(){
		if(empty($this->dbc)){
			$this->dbc = $this->connectDB();
		}

		return $this->dbc;
	}

	// Abstract Functions
	abstract protected 
	function connectDB();

	abstract public
	function getTableColumns($table_name);

	abstract public
	function execute($query, $params = array());

}