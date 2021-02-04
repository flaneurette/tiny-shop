<?php

// Cron.php

/* 
  Add a crontab through a linux terminal (sudo crontab -e) and link to /var/www/html/cron.php to empty the /session/ folder.
  !! this file is still experimental, and might not work on all instances. !!
*/

system("rm -f -R ../session/");
system("mkdir ../session/");
system("chmod session 0777");

?>
