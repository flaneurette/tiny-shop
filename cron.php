<?php
// cron.php

system("rm -f -R ../session/");
system("mkdir ../session/");
system("chmod session 0777");

?>
