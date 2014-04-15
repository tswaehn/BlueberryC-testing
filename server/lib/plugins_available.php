<?php

  class AbstractPlugin {
    
    public $name;
    public $caption;
    public $serverDir;
    public $clientDir;
    public $mandatory;
    public $version;
    public $active;
    
    function __construct( $name, $details = array() ){
      $this->name = $name;

      // read caption
      if (isset($details["caption"])){
	$this->caption = $details["caption"];
      } else {
	$this->caption = PLUGINS.$this->name;
      }
      
      // read serverDir
      if (isset($details["serverDir"])){
	$this->serverDir = $details["serverDir"];
      } else {
	$this->serverDir = PLUGINS.$this->name.'/';
      }

      // read clientDir
      if (isset($details["clientDir"])){
	$this->clientDir = $details["clientDir"];
      } else {
	$this->clientDir = PLUGINS.$this->name.'/';
      }

      // read version
      if (isset($details["version"])){
	$this->version = $details["version"];
      } else {
	$this->version = "1.0";
      }

      // read if system plugin
      if (isset($details["mandatory"])){
	$this->mandatory = $details["mandatory"];
      } else {
	$this->mandatory = 0;
      }
      
      // read state
      if (isset($details["active"])){
	$this->active = $details["active"];
      } else {
	$this->active = 1;
      }
      
    }

  }
  

  function createDefaultPluginList(){
    
    $plugins = array();
    
    // default server plugins
    $plugins[] = new AbstractPlugin( "echoBack", array("clientDir"=>"", "mandatory"=>1) );
    
    // default client plugins
    $plugins[] = new AbstractPlugin( "home", array("serverDir"=>"","mandatory"=>1) );
    $plugins[] = new AbstractPlugin( "plugins", array("serverDir"=>"","mandatory"=>1) );
    $plugins[] = new AbstractPlugin( "about", array("serverDir"=>"","mandatory"=>0) );
    
   
    return $plugins;
  }
  
  function saveAvailablePluginsToConfig( $plugins ){
    
    // convert each plugin object to an array
    $data = array();
    foreach ($plugins as $plugin){
      $name=$plugin->name;
      $serverDir=$plugin->serverDir;
      $clientDir=$plugin->clientDir;
      $version=$plugin->version;
      $mandatory=$plugin->mandatory;
      $active=$plugin->active;
      $data[$name] = array( "serverDir"=>$serverDir, "clientDir"=>$clientDir, "version"=>$version, "mandatory"=>$mandatory, "active"=>$active );
    }
    
    // store to file
    $content = json_encode( $data );
    file_put_contents( PLUGIN_LIST, $content );
    
    $src = PLUGIN_LIST;
    $dst = CLIENT.PLUGIN_LIST;
    
    echo "copy ".$src." to ".$dst."\n";
    copy( $src, $dst );
  }
  
  function readAvailablePluginsFromConfig(){
    
    $plugins = array();

    if (file_exists(PLUGIN_LIST)){
      $contents = file_get_contents( PLUGIN_LIST );
      
      $array = json_decode( $contents, true );
	
	foreach ($array as $name=>$details){
	  $plugins[$name] = new AbstractPlugin( $name, $details );
	}

    }
     
    return $plugins;
  }
  

  function getAvailablePlugins(){
    
    $plugins = array();
    
    $plugins = readAvailablePluginsFromConfig();
    
    if (empty($plugins)){
      // go into fall-back mode and create default plugin list
      $plugins = createDefaultPluginList();
      saveAvailablePluginsToConfig( $plugins );
      
    }
  
    return $plugins;
  }









?>
