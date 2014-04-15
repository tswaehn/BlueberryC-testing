<?php

  
  class EchoBack extends PluginBaseClass {
    
    function setup(){
      $this->log( "setup of echoBack complete" );
    }
    
    function shutdown(){
      $this->log("echoBack shutdown");
    }
    

  
    function interpreter( $action, $str ){
      $this->log("interpreting ".$action);

      // echo back 
      $this->sendData( $str );
    
    }
    
  
  }

  // generate object and add to global list
  $me = new EchoBack('echoBack');
  $masterTask->addTask( $me );


?>
 
