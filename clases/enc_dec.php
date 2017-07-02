<?php

	function encrypt($string, $key) {
	$result = '';
	for($i=0; $i<strlen($string); $i++) 
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
  	}
  	return base64_encode($result);
}
	
function decrypt($string, $key) 
{
	$result = '';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) 
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	return $result;
}

function random_string()
{
	$character_set_array = array( );
	$character_set_array[ ] = array( 'count' => 4, 'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
	$character_set_array[ ] = array( 'count' => 4, 'characters' => '0123456789' );
	$temp_array = array( );
	foreach ( $character_set_array as $character_set )
	{
		for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
		{
			$temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
		}
   }
	shuffle( $temp_array );
	return implode( '', $temp_array );
}

?>
