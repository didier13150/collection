<?php
	// GCStar item id start at 1
	$itemID = 1;
	$collectionID = 0;
	ob_start();
	if ( isset( $_GET['item'] ) and ! empty( $_GET['item'] ) )
	{
		$itemID = $_GET['item'];
	}
	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}

	include_once( 'class/collection.class.php' );
	include_once( 'settings.php' );

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
	$item = $collection->getItem( $itemID );
	if ( ! $item )
	{
		echo "Could not load collection's item !";
		return 127;
	}

?>
	<form>
		<img class="thumbnail-<?php echo $DETAIL_SIZE;?>" src="<?php echo $item->getThumbnail( $DETAIL_SIZE );?>" alt="<?php echo $item->title;?>">
		<div class="form-header">
			<label for="detail-ID">ID</label>
			<input type="text" id="detail-ID" value="<?php echo $item->id;?>">
			<label for="detail-title">Titre</label>
			<input type="text" id="detail-title" value="<?php echo htmlentities( $item->title );?>">
			<?php if( $item->originalTitle != "" ):?>
			<label for="detail-orig-title" >Titre Original</label>
			<input type="text" id="detail-orig-title" value="<?php echo htmlentities( $item->originalTitle );?>">
			<?php endif;?>
			<?php if( $item->date != "" ):?>
			<label for="detail-date" >Date</label>
			<input type="text" id="detail-date" value="<?php echo htmlentities( $item->date );?>">
			<?php endif;?>
			<?php if( $item->duration != "" ):?>
			<label for="detail-duration" >Dur&eacute;e</label>
			<input type="text" id="detail-duration" value="<?php echo htmlentities( $item->duration );?>">
			<?php endif;?>
			<?php if( $item->director != "" ):?>
			<label for="detail-director" >R&eacute;alisateur</label>
			<input type="text" id="detail-director" value="<?php echo htmlentities( $item->director );?>">
			<?php endif;?>
		</div>
		<div class="form-body">
			<div class="synopsis" id="detail-summary"><?php echo htmlentities( $item->synopsis );?></div>
			<?php if( count( $item->actors ) ):?>
			<div class="synopsis">Acteurs: <?php echo $item->getActorList();?></div>
			<?php endif;?>
		</div>
	</form>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>