<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	
	<div id="main-content-container-top" class="container">
		<div id="main-content-top-row" class="row">
			<div id="main-content-mission" class="twelvecol last">
				<?php  $a = new Area('MissionStatement'); $a->display($c);?>
			</div>
		</div>
	</div>
	
	<div id="main-content-container-bottom" class="container">
		<div id="main-content-bottom-row-fullwidth" class="row">
			<div id="main-content-twelvecol" class="twelvecol last">
				<?php  $a = new Area('MainContentTwelveCol'); $a->display($c);?>
			</div>
		</div>
		<div id="main-content-bottom-row-halwidth" class="row">
			<div id="main-content-sixcol-left" class="sixcol">
				<?php  $a = new Area('MainContentSixColLeft'); $a->display($c);?>
			</div>
			<div id="main-content-sixcol-right" class="sixcol last">
				<?php  $a = new Area('MainContentSixColRight'); $a->display($c);?>
			</div>
		</div>
	</div>
	<!-- end full width content area -->
	
<?php  $this->inc('elements/footer.php'); ?>
