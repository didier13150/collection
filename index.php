<?php
	include_once( 'settings.php' );
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
			collection = 0;
			getCollection();
			<?php foreach( $COLLECTIONS as $id => $collection_settings ):?>
				bindTab( '<?php echo $id;?>' );
			<?php endforeach;?>
		});
	</script>
</head>
<body>
	<noscript>
		<div>Pour utiliser cette page, vous devez activer les scripts Javascript.</div>
	</noscript>
	<header>
		<span class="logo" id="top"></span>
		<hgroup>
			<h1>Collection</h1>
			<h2>Vid&eacute;os</h2>
		</hgroup>
	</header>
	<nav>
		<?php foreach( $COLLECTIONS as $id => $collection_settings ):?>
		<a href="#" class="tab" id="tab-<?php echo $id;?>">
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
		<img class="mainloader" id="loader-img" src="img/ajax-loader.gif" alt="Please, Wait"/>
	</article>
	<footer>
		<p>Designed by Didier FABERT&nbsp;&copy;&nbsp;|&nbsp;Valid HTML5 and CSS3</p>
	</footer>
</body>
</html>