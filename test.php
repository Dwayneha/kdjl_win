<?php
$a1 = getenv('HTTP_X_FORWARDED_FOR');
$a2 = getenv('REMOTE_ADDR');
$a3 = getenv('SERVER_ADDR');

echo "HTTP_X_FORWARDED_FOR -- $a1 <br>"; 
echo "REMOTE_ADDR -- $a2 <br>"; 
echo "SERVER_ADDR -- $a3 <br>"; 
?>