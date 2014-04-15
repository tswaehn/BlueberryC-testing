<?php


 class PluginMaster extends PluginBaseClass {
 
    protected $tasks;
    protected $pluginConfig;
    
    // constructor
    function __construct( $who ){
     
      parent::__construct( $who );
      
      $this->tasks=array();
      $this->active=1;
    }
    
    // ======================
    // collection functions
    
    // add task to collection
    function addTask( $task ){
      $this->tasks[] = $task;  
    }
    
    function applyPluginConfig($pluginConfig){
      // store list
      $this->pluginConfig=$pluginConfig;
      
      // go through all config items and apply them to loaded plugins
      foreach( $this->pluginConfig as $config){
	
	$name=$config->name;
	$plugin = $this->findPlugin( $name );
	if (!empty($plugin)){
	
	  $plugin->active= $config->active;
	  $plugin->version= $config->version;
	  
	}
      }
      // done
    }

    // setup all plugins
    function setupAll(){
      
      //apply plugin info from file to plugins
      foreach ($this->tasks as $task){
	$name= $task->name;
	$this->log( "initiating setup of ".$name );
	
	// start if active
	if ($task->active){
	  $task->setup();
	} else {
	  $this->log( "plugin ".$name." is inactive" );
	}
      }
      
      $this->setup();
      
    }
    
    // stop all tasks (clean up)
    function shutdownAll(){
      
      foreach ($this->tasks as $task){
	$this->log( "initiating shutdown of ".$task->name );
	$task->shutdown();
      }
      
      $this->shutdown();
    
    }
    
    function processAll( $data ){
    
      foreach($this->tasks as $task){
	$this->log("processing ".$task->name );
	$task->process( $data );
      }
      
      $this->process( $data );
    
    }
      
    function findPlugin( $name ){

      if (strcmp( $this->name, $name)== 0){
	return $this;
      }
      
      foreach ($this->tasks as $task){
	if (strcmp( $task->name, $name)== 0){
	  return $task;
	}
      }
      
      return "";
    }
    
    // -----------------------------------------------
    // process new data
    function processData( $request ){
      global $output;
      
      // decode incoming request
      $obj=json_decode( $request, true );
      
      if (empty( $obj )){
	return "input empty";
      }
      if (!is_array( $obj )){
	return "input is not an array";
      }
      
      // there are several tasks
      foreach ($obj as $task){
      
	// each task has multiple actions for different plugins
	foreach ($task as $name=>$data){
	  
	  // find the suiting plugin
	  $plugin = $this->findPlugin( $name );
	  
	  // if non-empty
	  if (!empty($plugin)){

	    // valid and active plugin?
	    if (!$plugin->active){
	     $plugin->sendData("inactive");
	     break;
	    }
	    
	    if (!is_array( $data )){
	      $plugin->sendData("wrong format");
	      continue;
	    }
	    
	    foreach ($data as $action=>$actionData){
	      $plugin->interpreter( $action, $actionData );
	    }

	  }
	}
      }
      // output return data
      $o=$output;
      $output = "";
      return $o;
    }
    
    function interpreter( $action, $actionData ){
    
      
      switch($action){
	
	case 'socket': 
	
	      if (strcmp( $actionData, 'exit-socket' )== 0){
		// special return sequence, see BlueberryC-server.php
		$this->sendData('[bye, bye...]');
	      }
	      break;
	      
	case 'plugin':
	      $this->doPluginAction( $actionData );
	      break;
	      
	case 'plugin-list':
	      $this->returnPluginList();
	      break;
	      
	default:
	
	      $this->sendData("dont know what to do, be more precise");
      
      }
    
    }

    
    function doPluginAction( $actionData ){
      
      if (!is_array($actionData)){
	$this->sendData("no array");
	return;
      }
      
      if (isset($actionData["name"])){
	$name=$actionData["name"];
      } else {
      	$this->sendData("no name");
	return;
      }
      
      if (isset($actionData["do"])){
	$do=$actionData["do"];
      } else {
	$this->sendData("no do");
	return;
      }

      if (!isset($this->pluginConfig[$name])){
	$this->sendData("no valid plugin");
	return;
      }
      
      switch ($do){
      
	case "start": 	
		  // modify the runtime plugin
		  $plugin = $this->findPlugin( $name );
		  if (!empty($plugin)){
		    $plugin->active=1;
		  }
      		  
		  
		  // modify config
		  $this->pluginConfig[$name]->active=1;
		  saveAvailablePluginsToConfig( $this->pluginConfig );
		  break;
		  
	case "stop": 
		  // modify the runtime plugin
		  $plugin = $this->findPlugin( $name );
		  if (!empty($plugin)){
		    $plugin->active=0;
		  }
		  // modify config
		  $this->pluginConfig[$name]->active=0;;
		  saveAvailablePluginsToConfig( $this->pluginConfig );
		  break;
	
      }
    
    }
    
    function returnPluginList(){
      
      $list=array();
      
      foreach ($this->pluginConfig as $config){
	$list[$config->name]= array( 
		"name"=> $config->name,
		"server" => empty($config->serverDir)?0:1,
		"client" => empty($config->clientDir)?0:1,
		"active"=> $config->active,
		"version"=> $config->version,
		"mandatory"=> $config->mandatory );
      }
      
      
      $this->sendData( json_encode( $list ) );
    }
    
 }





?>
