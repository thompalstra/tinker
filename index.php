<?php
include("Hub/autoload.php");
$application = new \Hub\Application();
$application->run(new \Hub\Http\Request($_SERVER));
