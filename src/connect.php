<?php

namespace aorm;

abstract class connect{

	static $dbc;

	public
	function __construct($settings = array()){
        $defaults = array();
        $this->settings = array_merge($defaults, $settings);
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


}