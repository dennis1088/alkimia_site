<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

	<div id="main-content-container" class="container">
		<div class="row">
			<div id="main-content-inner" class="twelvecol last>
				<?php  print $innerContent; ?>		
			</div>
		</div>
	</div>
	
	<!-- end full width content area -->
	
<?php  $this->inc('elements/footer.php'); ?>
