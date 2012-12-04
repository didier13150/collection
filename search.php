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
		$chars = str_split( $str, 1 );
		$return = array();
		foreach( $chars as $char )
		{
			$return[] = $char;// . "\s*";
		}
		return join( '', $return );
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
	//$searches = explode( " ", $search );
	$hits = array();

	foreach( $items as $item )
	{
		$title = removeAccents( $item->title );
		/*foreach( $searches as $target )
		{
			if ( preg_match( "/$target/i", $title ) )
			{
				$hits[$target][] = $item;
			}
		}*/
		if ( preg_match( "/$search/i", $title ) )
		{
			$hits[] = $item;
		}
	}

	//$intersect = array_intersect( $hits[$searches[0]], $hits[$searches[1]] );
?>
<script language="javascript">
	<?php foreach( $hits as $id => $item ):?>
		bindItem( '<?php echo $item->id;?>' );
	<?php endforeach;?>
</script>
<form class="big">
<?php if( count( $hits ) ):?>
	<ul class="search">
	<?php foreach( $hits as $item ):?>
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
