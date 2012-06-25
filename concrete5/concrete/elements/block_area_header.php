<?php  
defined('C5_EXECUTE') or die("Access Denied.");
//$arHandle = strtolower(preg_replace("/[^0-9A-Za-z]/", "", $a->getAreaHandle()));
// add in a check to see if we're in move mode
$moveModeClass = "";
$c = $a->getAreaCollectionObject();
if ($c->isArrangeMode()) {
	$moveModeClass = "ccm-move-mode";
}
?>
<div id="a<?php echo $a->getAreaID()?>" cID="<?php echo $a->getCollectionID()?>" handle="<?php echo $a->getAreaHandle()?>" class="ccm-<?php  if ($a->isGlobalArea()) { ?>global-<?php  } ?>area <?php echo $moveModeClass?>">