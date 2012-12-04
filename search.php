<?php
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'settings.php' );

	function removeAccents( $accented )
	{
		$str = $accented;
		$str = preg_replace( "/à|â/i", "a", $str );
		$str = preg_replace( "/é|è|ê/i", "e", $str );
		$str = preg_replace( "/î/i", "i", $str );
		$str = preg_replace( "/ô/i", "o", $str );
		$str = preg_replace( "/û/i", "u", $str );
		$str = preg_replace( "/ç/i", "c", $str );
		return $str;
	}

	function getRegex( $str )
	{
		$worlds = preg_split( "/ /", $str, -1, PREG_SPLIT_NO_EMPTY );
		$return = array();
		foreach( $worlds as $world )
		{
			$chars = str_split( $world, 1 );
			$return[] = join( '\s*', $chars );
		}
		return join( '.*', $return );
	}

	$collectionID = 0;
	$sort = 'title';
	$search = null;

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	if ( isset( $_GET['search'] ) and ! empty( $_GET['search'] ) )
	{
		$search = removeAccents( $_GET['search'] );
	}
	ob_start();

	if( ! isset( $search ) )
	{
		echo "Nothing to search !";
		return 127;
	}

	$collection = null;
	if( $COLLECTIONS[$collectionID]['type'] == 'film' )
	{
		$collection = new FilmsCollection();
	}
	elseif( $COLLECTIONS[$collectionID]['type'] == 'series' )
	{
		$collection = new SeriesCollection();
	}

	if( ! isset( $collection ) )
	{
		echo $COLLECTIONS[$collectionID]['type'] . " is not a valid type !";
		return 127;
	}
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		echo $COLLECTIONS[$collectionID]['file'] . " file does not exists or is not readable !";
		return 127;
	}
	if ( ! $collection->setThumbsDir( $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		echo $COLLECTIONS[$collectionID]['thumbs-dir'] . " directory does not exists or is not readable !";
		return 127;
	}
	if ( ! $collection->load() )
	{
		echo "Could not load collection !";
		return 127;
	}
	$collection->sort( $sort );
	$items = $collection->getItems();
	$occurencies = array();
	/*
	$hits = array();
	$searches = preg_split( "/\s+/", $search, -1, PREG_SPLIT_NO_EMPTY );
	$nbOfWorld = str_word_count( $search );

	foreach( $items as $item )
	{
		for( $i = 0 ; $i < $nbOfWorld ; $i++ )
		{
			if ( preg_match( "/" . getRegex( $searches[$i] ) . "/i", removeAccents( $item->title ) ) )
			{
				$hits[$i][] = $item;
			}
		}
	}

	for( $i = 0 ; $i < ( $nbOfWorld - 1 ) ; $i++ )
	{
		$occurencies = array_intersect( $hits[$i], $hits[$i+1] );
	}
	if ( $nbOfWorld == 1 )
	{
		$occurencies = $hits[0];
	}*/
	$regex = getRegex( $search );
	echo "<!-- $regex -->";
	foreach( $items as $item )
	{
		if ( preg_match( "/$regex/i", removeAccents( $item->title ) ) )
		{
			$occurencies[] = $item;
		}
	}

?>
<script language="javascript">
	<?php foreach( $occurencies as $item ):?>
		bindItem( '<?php echo $item->id;?>' );
	<?php endforeach;?>
</script>
<form class="big">
<?php if( count( $occurencies ) ):?>
	<ul class="search">
	<?php foreach( $occurencies as $item ):?>
		<li class="search">
			<a href="#" class="item" id="item-<?php echo $item->id;?>">
				<?php echo $item->title;?>
			</a>
			de <?php echo $item->director;?> (<?php echo $item->year;?>)
		</li>
	<?php endforeach;?>
	</ul>
<?php else:?>
	<span class="error">Aucun film ne correspond à la demande ("<?php echo $search;?>")</span>
<?php endif;?>
</form>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>
