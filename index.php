<?php

	include_once( 'settings.php' );
	include_once( 'include/functions.php' );
	session_start();

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$_SESSION['collection'] = $_GET['collection'];
	}
	else
	{
		$_SESSION['collection'] = 0;
	}
	if ( isset( $_GET['page'] ) and ! empty( $_GET['page'] ) )
	{
		$_SESSION['page'] = $_GET['page'];
	}
	else
	{
		$_SESSION['page'] = 0;
	}
		if ( isset( $_GET['item'] ) and ! empty( $_GET['item'] ) )
	{
		$_SESSION['item'] = $_GET['item'];
	}
	if ( isset( $_GET['query'] ) and ! empty( $_GET['query'] ) )
	{
		$query = $_GET['query'];
	}
	ob_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Collection</title>
	<meta name="AUTHOR" content="Didier Fabert"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="icon" type="image/x-icon" href="favicon.ico"/>
	<link type="text/css" rel="stylesheet" href="css/shared.css"/>
	<link type="text/css" rel="stylesheet" href="css/screen.css" media="screen"/>
	<!-- <link type="text/css" rel="stylesheet" href="css/print.css" media="print"/> -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	<script type="text/javascript" src="js/iutil.js"></script>
	<script type="text/javascript" src="js/idrag.js"></script>
	<script type="text/javascript">
		$(document).ready( function() {
			init();
			modifyRef();
			collection = 0;
			sortBy = '<?php echo 'title';?>';
			defaultSortBy = '<?php echo 'title';?>';
			searchDefault = '<?php echo 'Chercher un film';?>';
			getCollection();
			<?php foreach( $COLLECTIONS as $id => $collection_settings ):?>
				bindTab( '<?php echo $id;?>' );
			<?php endforeach;?>
			bindSearch();
			setSearchDefault( false );
		});
	</script>
</head>
<body>
	<header>
		<span class="logo" id="logo-top"></span>
		<hgroup>
			<h1>Collection</h1>
			<h2>Vid&eacute;o</h2>
		</hgroup>
		<form id="search-box" method="GET" target="index.php">
			<input type="text" id="search-text" value="" name="search">
			<input type="submit" value="Chercher" id="search-btn">
			<input type="hidden" name="query" value="search" class="input-hidden">
			<input type="hidden" name="collection" value="<?php echo $_SESSION['collection'];?>" class="input-hidden">
		</form>
	</header>
	<nav>
		<?php foreach( $COLLECTIONS as $id => $collection_settings ):?>
		<a href="index.php?query=collection&collection=<?php echo $id;?>" class="tab<?php if( $id == $_SESSION['collection'] ) echo " current-tab";?>" id="tab-<?php echo $id;?>">
			<span id="icon-<?php echo $id;?>" class="icon icon-video"></span><?php echo htmlentities( $collection_settings['title'] );?>
		</a>
		<?php endforeach;?>
	</nav>
	<div id="popup">
		<p>
			<span id="popup-title">Collection - D&eacute;tails</span>
			<span class="right">
				<span class="icon icon-move"></span>
				<span class="icon icon-close"></span>
			</span>
		</p>
		<div id='details'></div>
	</div>
	<article>
		<noscript>
			<?php if ( isset( $query ) ) include verify( $query ); ?>
		</noscript>
	</article>
	<footer>
		<p>Designed by Didier FABERT&nbsp;&copy;&nbsp;|&nbsp;Valid HTML5 and CSS3</p>
	</footer>
	<noscript>
		<div class="noscript">Pour une utilisation optimale, vous devez activer les scripts Javascript.
		</div>
	</noscript>
</body>
</html>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>