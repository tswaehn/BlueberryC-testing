#!/usr/bin/php
<?php

  include('./config/config.php');
  include('./lib/checkConfig.php');
  include('./lib/plugins.php');

  // Set time limit to indefinite execution
  set_time_limit (0);

  // Set the ip and port we will listen on
  $address = SOCKET_HOST;
  $port = SOCKET_PORT;
  $max_clients = 10;

  // Array that will hold client information
  $clients = Array();
  for ($i=0;$i<$max_clients;$i++){
    $clients[$i]['sock']=null;
  }
  
  $read=array();
  
  // Create a TCP Stream socket
  $socket = socket_create(AF_INET, SOCK_STREAM, 0);
  
  if ($socket === false) {
      $errorcode = socket_last_error();
      $errormsg = socket_strerror($errorcode);
      
      die("Couldn't create socket: [$errorcode] $errormsg");
  }
  
  // Bind the socket to an address/port
  @socket_bind($socket, $address, $port) or die('Could not bind to address '.$address.':'.$port."\n");

  // Start listening for connections
  socket_listen($socket);

  //
  $masterTask->setupAll();
  
  // Loop continuously
  echo "ready for connections\n";
  
  while (true) {

    // Setup clients listen socket for reading
    $read[0] = $socket;

    for ($i = 0; $i < $max_clients; $i++) {

	  if ($clients[$i]['sock']  != null){
	      $id = $clients[$i]['sock'] ;
	      echo 'read from '.$id."\n";
	      $read[$i + 1] = $id;
	  }

    }

    // Set up a blocking call to socket_select()
    $write=null;
    $except=null;
    
    $num_changed_sockets = socket_select($read,$write,$except, $sek=5);
    if ($num_changed_sockets == false){
      echo "nothing to do, ... \n";
      continue;
    }
    
    if ($num_changed_sockets > 0){
      $new_socket= socket_accept( $socket );
      

      $socket_error=0;
      $data = "";
      $starttime=time();
      
      do {
	$package = socket_read( $new_socket, 512, PHP_BINARY_READ );
	$socket_error = socket_last_error($new_socket);
	
	$data .= $package;
	$msg_start=strpos( $data, "<CONTENT>" );
	$msg_end=strpos( $data, "</CONTENT>" );

	if (($socket_error == 0) && (empty($package))){
	  // create a socket error if nothing usefull received;
	  $socket_error=96;
	  break;
	}
	
	
      } while ( ( $msg_end === false ) && ($socket_error == 0) );

      // return received data
      echo $data."\n";
      
      if ($socket_error == 0){
	
	// strip content tags
	$data = preg_replace("/<CONTENT>|<\/CONTENT>/", "", $data );

	// 
	$response = $masterTask->processData( $data );


	// prepare response
	$response = "<RESPONSE>".$response."</RESPONSE>";
	echo $response."\n";
	// send response
	socket_write( $new_socket, $response );
	
	if (strpos( $response, "[bye, bye...]")>0){
	  socket_close( $new_socket );
	  socket_shutdown($socket, STREAM_SHUT_WR); 
	  break;
	}     
	
      } else {
	// stopped reading with an error
	echo "socket error ".$socket_error."\n";
      }
      
      socket_close( $new_socket );
    
    }


} // end while

  // shutdown plugins
  $masterTask->shutdownAll();

  // Close the master socket
  socket_close($socket);
  
  echo "closed socket forever\n";
  echo "exiting";
?>
 
