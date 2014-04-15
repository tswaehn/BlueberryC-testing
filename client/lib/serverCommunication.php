<?php

  class ServerComm {
  
    protected $fp;
    
    
    function __construct(){
    
      $this->fp = @fsockopen( 'localhost', 9000, $errno, $errstr, 5000 );
      
      if (!$this->fp){
	echo "error no socket <br>";
	echo $errno. " " .$errstr;
	return;
      } else {
	
	echo "connected to server<br>";
      }
    
    }
    
    function __destruct(){
      
      fclose($this->fp);
      echo "disconnected server<br>";
      
    }
    
    static function responseToArray( $response ){
      // remove plugin module ... ex. "(master)"
      $response = preg_replace("/\(.*?\)/", "", $response);
      
      // decode to array 
      $obj=json_decode( $response, true );
      
      return $obj;
    }
    
    static function addTask(  $plugin, $action, $text, $obj=array()  ){
    
      $obj[] = array( $plugin => array( $action => $text ) );
      
      return $obj;
    }

    function transferDataToSocket( $objArray ){
      
      if ($this->fp===false){
	echo "no connection to server<br>";
	return;
      }
      
      $data = json_encode( $objArray );
      
      $data = "<CONTENT>".$data."</CONTENT>";

      // send complete message
      fwrite( $this->fp, $data  );

      // now wait for the result/response
      $response = "";
      do {
	
	$info = stream_get_meta_data($this->fp);

	$package = fread( $this->fp, 10 );
	$response .= $package;

	$msg_start=strpos( $response, "<RESPONSE>" );
	$msg_end=strpos( $response, "</RESPONSE>" );

      } while ((!$info['timed_out']) && ($msg_end === false));
      
      if ($msg_end === false){
	echo "failed to send data";
      } else {
	// strip content tags
	$response = preg_replace("/<RESPONSE>|<\/RESPONSE>/", "", $response );
	echo $response;
      }
      
      return $response;
    }
      
  
  }
  
  $server = new ServerComm();
?>
