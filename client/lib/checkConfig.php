<?php

  if (!is_writeable(RUNTIME_CONFIG)){
    echo "cannot edit runtime config";
    die();
  }

  if (!is_dir(PLUGINS)){
    echo "missing plugin directory ".PLUGINS;
    die();
  }



?>
