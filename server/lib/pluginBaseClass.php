<?php

  $output="";
  
  class PluginBaseClass {
    
    protected $name;
    protected $active;
    protected $version;
    
    function __construct( $who ){
      
      $this->name = $who;
      $this->active=0;
      $this->version="0.0";
      
      $this->log( "creating ".$this->name );
	
    }

    // overrided by extended classes
    function interpreter( $action, $str ){
      
      
      switch( $action ){
      
	default:
	
	    $this->sendData("Action is '".$action."'" );
	    $this->sendData("Data is '".$str."'");
	    $this->sendData("Dont know what to do with that");
	
      }
    }

    // overrided by extended classes
    function setup(){
      $this->log("nothing to do");
    }
    
    // general:
    function shutdown(){
      $this->log("nothing to do");
    }
    
    
    // general: 
    function sendData( $out ){
      global $output;
    
      $str = "(". $this->name .")".$out."\n";
      $output .= $str;  
    }    
    
    // general:
    function log( $str ){
      echo $this->name.">".$str."\n";      
    }

  }



?> 
