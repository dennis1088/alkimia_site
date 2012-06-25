<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	
	<div id="main-content-container-top" class="container">
		<div id="main-content-bg-image" class="row">
			<div id="quote-container-row" class="row">
				<div class="twelvecol last">
					<?php  
					$a = new Area('Quote');
					$a->display($c);
					?>
				</div>
			</div>
			<div id="main-content-events" class="row">
				<div class="threecol event-block"></div>
				<div class="threecol event-block"></div>
				<div class="threecol event-block"></div>
				<div class="threecol event-block last"></div>
			</div>

		</div>
	
	</div>
	<div class="container main-content-border-shadow" style="border-bottom-width:2px;border-style:dashed;border-color:white;"></div>
	<div class="container main-content-border-shadow"></div>

	<div id="main-content-container-bottom" class="container">
		<div id="main-content-inner-bottom" class="row">
			<div id="main-content-area-bottom" class="sixcol">
				<?php  
				$a = new Area('LeftSpace');
				$a->display($c);
				?>
			</div>
			<div id="main-content-area-bottom" class="sixcol last">
				<?php  
				$a = new Area('RightSpace');
				$a->display($c);
				?>
			</div>
		</div>
	
	</div>
	<div class="container main-content-border-shadow" style="border-bottom-width:2px;border-style:dashed;border-color:white;"></div>
	<!-- end full width content area -->
	
<?php  $this->inc('elements/footer.php'); ?>
