 
<?php

  extract( $_GET, EXTR_PREFIX_ALL, "url" );
  extract( $_POST, EXTR_PREFIX_ALL, "url" );

 
  function getGlobal( $var ){
    
    if (!isset($GLOBALS[$var])){
      $ret='';
    } else {
      $ret=$GLOBALS[$var];
    }

    return $ret;
  }

  function getUrlParam( $var ){
  
    $urlParam = 'url_'.$var;
    
    $ret = getGlobal( $urlParam );
    return $ret;
  }
  
  function addTask(  $plugin, $action, $text, $obj=array()  ){
  
    $obj[] = array( $plugin => array( $action => $text ) );
    
    return $obj;
  }
  
  function encodeRequest( $obj ){
    
    $request = json_encode( $obj );
    
    $request = "<CONTENT>".$request."</CONTENT>";
    return $request;
  }
  


  $plugin = getUrlParam('plugin');
  $action=getUrlParam('action');
  $text=getUrlParam('text');
  
  $multiple = getUrlParam('multiple');
  
    if (!empty($action)){
      // only execute if there is something to do
      
      $fp = @fsockopen( 'localhost', 9000, $errno, $errstr, 10000 );
      
      if (!$fp){
	echo "error no socket <br>";
	echo $errno. " " .$errstr;

      } else {
      
	echo '<pre style="border:thin solid blue;padding:10px;">';

	  // ---------
	  // send data
	  
	  stream_set_timeout($fp, 5000 );
	
	  
	  if ($multiple != ""){
	    $obj = addTask('rs232','rx','');
	    $obj = addTask('rs232','tx','hello world', $obj);
	    $obj = addTask('rs232','rx','', $obj);
	    
	    $request = json_encode( $obj );
	    fwrite( $fp, $request );

	  } else {
	    $obj=addTask($plugin, $action, $text);
	    
	    fwrite( $fp, encodeRequest( $obj )  );
	  }

	  // ---------
	  // now wait for the result/response
	    $response = "";
	    do {
	      
	      $info = stream_get_meta_data($fp);

	      $package = fread( $fp, 5000 );
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
	    
	    fclose( $fp );

	echo '</pre>';
      }
    }

 
    


?>
 
 
<form action="" method="GET">
  <select name="plugin">
    <option value="master">master</option>
    <option value="echoBack">echoBack</option>
    <option value="rs232">rs232</option>
    
  </select>

  <input type="edit" name="action" value="<?php echo $action; ?>">
  <input type="edit" name="text" value="<?php echo $text; ?>">
  
<input type="submit" value="send">
</form>


<form action="" method="GET">
<input type="hidden" name="plugin" value="master">
<input type="hidden" name="action" value="socket">
<input type="hidden" name="text" value="exit-socket">
<input type="submit" value="close server">
</form>


<form action="" method="GET">
<input type="hidden" name="multiple" value="exit-socket">
<input type="submit" value="multiple actions">
</form>

