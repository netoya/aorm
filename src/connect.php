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
		if(empty(static::$dbc)){
			static::$dbc = $this->connectDB();
		}

		return static::$dbc;
	}

	// Abstract Functions
	abstract protected 
	function connectDB();

	abstract public
	function geTableColumns();

	abstract public
	function execute();

}