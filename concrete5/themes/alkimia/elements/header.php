<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE?>">

<head>

<?php   Loader::element('header_required'); ?>

<!-- Site Header Content //-->
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- 1140px Grid styles for IE -->
	<!--[if lte IE 9]><link rel="stylesheet" href="<?php  echo $this->getThemePath(); ?>/css/ie.css" type="text/css" media="screen" /><![endif]-->

<!-- The 1140px Grid - http://cssgrid.net/ -->
	<link rel="stylesheet" href="<?php  echo $this->getThemePath(); ?>/css/1140.css" type="text/css" media="screen" />

<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
	<script type="text/javascript" src="<?php  echo $this->getThemePath(); ?>/js/css3-mediaqueries.js"></script>

<link rel="stylesheet" media="screen" type="text/css" href="<?php  echo $this->getStyleSheet('main.css')?>" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php  echo $this->getStyleSheet('typography.css')?>" />
<link href='http://fonts.googleapis.com/css?family=Merriweather:400,700,900,300' rel='stylesheet' type='text/css' />


</head>

<body>

<!--start main container -->

	<div id="main-header" class="container">
		<div id="header-image" class="row">
			<div id="header-image-nav" class="twelvecol last"></div>
			<div id="shield"></div>
			<div id="nav-links" class="row">
				<div class="twelvecol last">
				<?php  
					$a = new Area('NavLinks1');
					$a->display($c);
					?>
				</div>
				
			</div>
		</div>
		
	</div>

