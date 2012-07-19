<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	
		<div class="row">
			<div id="callout">
				<div class="onecol"></div>
				<div class="tencol">
					<?php  $a = new Area('MissionStatement'); $a->display($c);?>
				</div>
				<div class="onecol last"></div></div>
		</div>
		<div class="row">
			<div id="events">
				<div class="twelvecol">
					<?php  $a = new Area('MainContentTwelveCol'); $a->display($c);?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="sixcol">
				<?php  $a = new Area('MainContentSixColLeft'); $a->display($c);?>
			</div>
			<div class="sixcol last">
					<?php  $a = new Area('MainContentSixColRight'); $a->display($c);?>
			</div>
		</div>


	<!-- end full width content area -->
	
<?php  $this->inc('elements/footer.php'); ?>
