
<h1>Web Site Configuration Settings Setup</h1>

<form action="" name="config_form" method="post">
<h3>Web Settings</h3>
<p>
Web Site Domain:<br />
<input type="text" name="config_web_site_domain" value="" />
</p>
<p>
Web Site Root:<br />
<input type="text" name="config_web_root" value="" />
</p>
<h3>Database Settings</h3>
<p>
Host:<br />
<input type="text" name="config_host" value="" />
</p>
<p>
Database:<br />
<input type="text" name="config_database" value="" />
</p>
<p>
Database Prefix:<br />
<input type="text" name="config_database_prefix" value="" />
</p>
<p>
Username:<br />
<input type="text" name="config_username" value="" />
</p>
<p>
Password:<br />
<input type="password" name="config_password" value="" />
</p>
<p>
<input type="submit" name="config_submit" value="Process" />
<input type="hidden" name="config" value="true" />
</p>
</form>

<?php

$config = $_POST['config'];

if($config == true) {

# Load the config file into a variiable and open it using the "simplexml_load_file" function
$file = "config.xml";
$xml = simplexml_load_file($file);

# Web configuration settings
$xml->web_configuration[0]->web_domain[0] = $_POST['config_web_site_domain'];
$xml->web_configuration[0]->web_root[0] = $_POST['config_web_root'];

# Database configuration settings
$xml->database_configuration[0]->host[0] = $_POST['config_host'];
$xml->database_configuration[0]->database[0] = $_POST['config_database'];
$xml->database_configuration[0]->database_prefix[0] = $_POST['config_database_prefix'];
$xml->database_configuration[0]->username[0] = $_POST['config_username'];
$xml->database_configuration[0]->password[0] = $_POST['config_password'];

# Push the changes to the configuration file
file_put_contents("config.xml", $xml->asXML());

# Output the configuration settings to the screen
echo "<h3>Your Settings</h3> \n";
echo 'Web Site Domain: ' . $xml->web_configuration->web_domain . "<br> \n";
echo 'Web Site Root: ' . $xml->web_configuration->web_root . "<br> \n";
echo 'Database Host: ' . $xml->database_configuration->host . "<br> \n";
echo 'Database: ' . $xml->database_configuration->database . "<br> \n";
echo 'Database Username: ' . $xml->database_configuration->username . "<br> \n";
echo 'Database Password: ' . $xml->database_configuration->password . "<br> \n";

}

?>
