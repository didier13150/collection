<?php
	function removeAccents( $accented )
	{
		$str = $accented;
		$str = preg_replace( "/à|â|ä/i", "a", $str );
		$str = preg_replace( "/é|è|ê|ë/i", "e", $str );
		$str = preg_replace( "/î|ï/i", "i", $str );
		$str = preg_replace( "/ô|ö/i", "o", $str );
		$str = preg_replace( "/û|ü/i", "u", $str );
		$str = preg_replace( "/ç/i", "c", $str );
		return $str;
	}

	function getRegex( $str )
	{
		$worlds = preg_split( "/\s+/", $str, -1, PREG_SPLIT_NO_EMPTY );
		$regex = array();
		foreach( $worlds as $world )
		{
			$chars = str_split( $world, 1 );
			$regex[] = join( '\s*', $chars );
		}
		return join( '.*', $regex );
	}

	function verify( $script )
	{
		$available = array( 'collection', 'details', 'search', 'index' );
		if ( in_array( $script, $available ) )
		{
			return $script . '.php';
		}
		return 'index.php';
	}
?>