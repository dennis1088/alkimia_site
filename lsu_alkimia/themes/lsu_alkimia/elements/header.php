<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE?>">

<head>

<?php   Loader::element('header_required'); ?>

	<meta charset="utf-8" />
	<!-- Site Header Content //-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- 1140px Grid styles for IE -->
	<!--[if lte IE 9]><link rel="stylesheet" href="<?php  echo $this->getThemePath(); ?>/css/ie.css" type="text/css" media="screen" /><![endif]-->

	<!-- The 1140px Grid - http://cssgrid.net/ -->
	<link rel="stylesheet" href="<?php  echo $this->getThemePath(); ?>/css/1140.css" type="text/css" media="screen" />

	<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
	<script type="text/javascript" src="<?php  echo $this->getThemePath(); ?>/js/css3-mediaqueries.js"></script>

	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('main.css')?>"/>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('css/styles.css')?>"/>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('css/font-awesome.css')?>"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="<?php  echo $this->getThemePath(); ?>/js/fittext/jquery.fittext.js"></script>
	<script src="<?php  echo $this->getThemePath(); ?>/js/lettering/jquery.lettering.js"></script>
	<script type="text/javascript">
	$("#fittext1").fitText(0.8);
	$("#fittext1").lettering('words');
	$("#fittext2").fitText(0.8);
	$(".section-heading").lettering('words');
	</script>
</head>

<body>

<!--start main container -->

	<div class="container">
		<div class="row">
			<div id="header">
				<div class="sevencol">
					<?php $a = new GlobalArea('Logo'); $a->display($c);?>
				</div>
				<div class="fivecol last">
					<div id="nav">
						<?php $a = new GlobalArea('Header-Nav'); $a->display($c);?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	

