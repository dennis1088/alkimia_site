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

<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('main.css')?>"/>
<link rel="stylesheet" media="screen" type="text/css" href="<?php  echo $this->getStyleSheet('typography.css')?>" />
<link href='http://fonts.googleapis.com/css?family=Merriweather:400,700,900,300' rel='stylesheet' type='text/css' />


</head>

<body>

<!--start main container -->

	<?php
	$u = new User();
	if ($u->checkLogin() ) {
	echo '<div id="login-bar-container" class="container">';
		echo '<div id="login-bar-row" class="row">';
			echo '<div id="login-bar-col" class="twelvecol last">';
			$a = new Area('LoginBar'); $a->display($c);
			echo '</div>';
		echo '</div>';
	echo '</div>';
	}
	?>
	<div id="main-header" class="container" <?php if ($u->checkLogin()){ echo 'style="padding-top:45px;"';}?>>
		<div id="main-header-inner-row" class="row">
			<div id="main-header-logo" class="threecol logo">
				<?php $a = new GlobalArea('Logo'); $a->display($c);?>
			</div>
			<div class="ninecol last"></div>
		</div>
		<div id="main-header-image-row" class="row">
			<div id="main-header-image" class="twelvecol last">
				<?php $a = new GlobalArea('Header-image'); $a->display($c);?>
			</div>
		</div>
	</div>

