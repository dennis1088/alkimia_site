<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
	

	<div id="main-content-container" class="container">
		<div id="main-content-inner" class="row">
			<div class="fourcol">	
			<?php  
			$a = new Area('Sidebar');
			$a->display($c);
			?>
			</div>
			<div class="eightcol last">
			<?php  
			$a = new Area('Main');
			$a->display($c);
			?>
			</div>
		</div>
	</div>
	<!-- end sidebar -->
	
<?php  $this->inc('elements/footer.php'); ?>
