<div style="min-height:100px;">

</div>

    <ul>
    
    <?php
      
      function renderItem($name, $caption){
	// workaround for icon
	$id="home";
	//
	echo '<li><a href="?app='.$name.'"><span id="'.$id.'">'.$caption.'</span></a></li>';
      }
      
      if (isset($plugins)){
      /*
	<li><a href="?app=home"><span id="home">home</span></a></li>
	<li><a href="?app=copiino"><span id="home">CoPiino</span></a></li>
	<li><a href="?app=rs232terminal"><span id="home">RS232</span></a></li>
        <li><a href="?app=controls"><span id="home">controls</span></a></li>
	<li><a href="?app=preferences"><span id="home">preferences</span></a></li>
	<li><a href="?app=test"><span id="home">-test-</span></a></li>
	<li><a href="?app=about"><span id="home">about</span></a></li>
	*/
	foreach ($plugins as $plugin){
	  $name = $plugin->name;
	  $clientDir = $plugin->clientDir;
	  
	  if (!empty($clientDir)){
	    if ($plugin->active){
	      renderItem( $name, $name );
	    }
	  }
	}
      } else {
	
	echo "<li>failed</li>";
      }
      ?>
      
    </ul>

<div style="min-height:1000px;">

</div>
