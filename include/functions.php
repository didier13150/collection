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

	function getRegex( $str, $full = false )
	{
		$worlds = preg_split( "/\s+/", $str, -1, PREG_SPLIT_NO_EMPTY );
		if ( $full )
		{
			return join( '.*', $worlds );
		}
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

	function getThumbnailWidth( $thumb_size )
	{
		return $SIZE_OF_THUMBNAILS[$thumb_size]['width'];
	}

	function getThumbnailHeight( $thumb_size )
	{
		return $SIZE_OF_THUMBNAILS[$thumb_size]['height'];
	}

	function getAllCombinations( $target, $join = ' ' )
	{
		$combinations = array();

		$worlds = preg_split( "/\s+/", $target, -1, PREG_SPLIT_NO_EMPTY );
		$count = count($worlds);
		$possibilities = pow($count, $count);

		$cpt = 0;

		for( $i = 0; $i < $possibilities; $i++ )
		{
			$sameLenght = base_convert( $i, 10, $count );
			while ( strlen( $sameLenght ) < $count )
			{
				$sameLenght = '0'.$sameLenght;
			}
			$id = str_split( $sameLenght );

			if ( $id == array_unique( $id ) )
			{
				$possibility = '';
				foreach ( $id as $item )
				{
					if( $possibility != '' )
					{
						$possibility .= $join;
					}
					$possibility .= $worlds[$item];
				}
				$combinations[] = $possibility;
			}
		}
		return $combinations;
	}

	function i18n2html( $text )
	{
		return preg_replace( '/\'/', '\\\'', T_( $text ) );
	}
?>