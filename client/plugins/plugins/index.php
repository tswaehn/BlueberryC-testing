<?php 
 
 $action = getUrlParam('action');
 
 switch ($action){
    
    case 'install': include( PLUGIN_DIR.'install.php');
		    break;
    case 'ask_install': include( PLUGIN_DIR.'ask_install.php');
		    break;
    
    case 'stop': 
		  $plugin=getUrlParam("plugin");
		  $obj = ServerComm::addTask( 'master', 'plugin', array("do"=>"stop","name"=>$plugin) );
		  $server->transferDataToSocket( $obj );
		  //include( PLUGIN_DIR.'local.php' );
		  break;
    case 'start': 
		  $plugin=getUrlParam("plugin");
		  $obj = ServerComm::addTask( 'master', 'plugin', array("do"=>"start","name"=>$plugin) );
		  $server->transferDataToSocket( $obj );
		  //include( PLUGIN_DIR.'local.php' );
		  
		  break;
		  
    default:
	      include( PLUGIN_DIR.'local.php' );
  
  }
  
  ?>

  
