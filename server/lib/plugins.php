<?php

  include('pluginBaseClass.php');
  include('pluginMaster.php');
  include('plugins_available.php');
  
  
  $masterTask = new PluginMaster('master');


  $pluginConfig = getAvailablePlugins();
  
  foreach ($pluginConfig as $item){
    $serverDir=$item->serverDir;
    
    if (!empty($serverDir)){
      include( $serverDir.'plugme.php');
    }
    
  
  }

  // apply config to loaded plugins
  $masterTask->applyPluginConfig( $pluginConfig );
  
?> 
