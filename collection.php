<?php
	include_once( 'class/collection.class.php' );
	include_once( 'settings.php' );

	$collectionID = 0;
	$error = null;
	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	ob_start();

	$collection = new VideoCollection();
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
	$items = $collection->getItems();
	$maxItem = count( $items );
?>
<script language="javascript">
			availableItems = new Array();
			<?php foreach( $items as $id => $film ):?>
			bindThumbnail( '<?php echo $film->id;?>' );
			<?php endforeach;?>
			resizeArticle();
</script>
<p>
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