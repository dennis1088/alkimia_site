<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui">
<?php 
$bo = Block::getByID($bOriginalID);
$bp = new Permissions($bo);
if ($bp->canWrite()) {
	$bv = new BlockView(); ?>
	
		<div class="alert-message block-message info" style="margin-bottom: 10px" ><p><?php echo t("This block was copied from another location. Editing it will create a new instance of it.")?></p></div>

	<?php 
	
	$bv->render($bo, 'edit', array(
		'c' => $c,
		'a' => $a,
		'proxyBlock' => $b
	));
} else { ?>
	<div class="alert-message error"><?php echo t("You don't have access to edit the original instance of this block.")?></div>
<?php  } ?>
</div>