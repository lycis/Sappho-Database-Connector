<?php
... (initialize SDBC interface)

// issue statement to database
$exec = $sdbc->execute('CREATE TABLE temp(temp_id INT AUTO_INCREMENT, temp_data BLOB)');

// check if execution was successfull
if($exec) die("An error occured - $exec: ".$sdbc->lastError());

...
?>