<?php
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'settings.php' );

	$collectionID = 0;
	$start = 0;
	$sort = 'title';

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	if ( isset( $_GET['start'] ) and ! empty( $_GET['start'] ) )
	{
		$start = $_GET['start'];
	}
	if ( isset( $_GET['sort'] ) and ! empty( $_GET['sort'] ) )
	{
		$sort = $_GET['sort'];
	}
	ob_start();

	// page count begins at 1
	//$start--;
	$start *= $NB_ITEM_PER_PAGE;
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
	$items = $collection->getItems( $start, $NB_ITEM_PER_PAGE );
	$maxItem = $collection->count();
?>
<script language="javascript">
			<?php foreach( $items as $id => $film ):?>
			bindThumbnail( '<?php echo $film->id;?>' );
			<?php endforeach;?>
			<?php for( $i = 0 ; $i <= intval( $maxItem / $NB_ITEM_PER_PAGE ) ; $i++ ):?>
			bindPage( '<?php echo $i;?>' );
			<?php endfor;?>
			resizeArticle();
</script>
<p>
	<span>Page
	<?php for( $i = 0 ; $i <= intval( $maxItem / $NB_ITEM_PER_PAGE ) ; $i++ ):?>
		<?php if ( $i != 0 ) echo "|";?>

		<?php if( ( $i * $NB_ITEM_PER_PAGE ) == $start ):?>
			<span class="page-selected">&nbsp;<?php echo $i;?>&nbsp;</span>
		<?php else:?>
			&nbsp;<a href="#" class="<?php echo "page";?>" id="page-<?php echo $i;?>"><?php echo $i;?>&nbsp;</a>
		<?php endif;?>
	<?php endfor;?>
	<span>
	<span class="right collection-data"><?php echo $maxItem;?> &eacute;l&eacute;ment<?php if ( count( $items ) > 1 ) echo 's';?> dans la collection</span>
</p>
<div id="thumbnails">
	<?php foreach( $items as $id => $film ):?>
	<a href="#" class="item thumbnail-container" id="item-<?php echo $film->id;?>">
	<img class="thumbnail-<?php echo $THUMB_SIZE;?>" src="<?php echo $film->getThumbnail( $THUMB_SIZE );?>" alt="<?php echo $film->title;?>" title="<?php echo $film->title;?>">
	</a>
	<?php endforeach;?>
</div>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>