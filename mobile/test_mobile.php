<?php

require_once ("../clases/Mobile_Detect.php");
	
$detect = new Mobile_Detect;
$device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$script_version = $detect->getScriptVersion();

echo "Device: ".$device_type;
?>
