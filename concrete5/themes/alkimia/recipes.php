<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	
	<div id="main-content-container-top" class="container">
		<div id="main-content-bg-image" class="row">
			<div id="quote-container-row" class="row">
				<div class="twelvecol last">
					<?php  
					$a = new Area('Recipes');
					$a->display($c);
					?>
				</div>
			</div>
		</div>
	
	</div>
	<!-- end full width content area -->
	
<?php  $this->inc('elements/footer.php'); ?>
