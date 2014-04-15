<?php
  
 function render( $plugin ){
   
   $mod="";
   if ($plugin["mandatory"] == 0){
    $action = ($plugin["active"])?"stop":"start";
    $url = "";
    $mod = '<a href="'.linkToMe( $action ).'&plugin='.$plugin["name"].'">('.$action.')</a>';
   }
   
   echo "<tr>";
    echo "<td>".$plugin["name"]."</td>";
    echo "<td>".$plugin["server"]."</td>";
    echo "<td>".$plugin["client"]."</td>";
    echo "<td>".$plugin["version"]."</td>";
    echo "<td>".$plugin["active"]." ".$mod."</td>";
 
   echo "<tr>";
 }
  
 echo "<h3>Plugin List</h3>";

 $obj = ServerComm::addTask( 'master', 'plugin-list', '' );
 
 $response = $server->transferDataToSocket( $obj );

 $array= ServerComm::responseToArray( $response );
 
 echo "<table>";
 echo "<tr><th>name</th><th>server</th><th>client</th><th>version</th><th>active</th></tr>";
 foreach ($array as $item){
  render( $item );
 
 }
 
 echo "</table>";
?>
