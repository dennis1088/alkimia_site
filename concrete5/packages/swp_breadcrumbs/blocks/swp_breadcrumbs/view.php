<?php    defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div class="swp-breadcrumbs">
<a href="<?php  echo $homePageLink; ?>"><?php   echo SITE; ?></a>
<?php   
$sublevels = $this->controller->getSubLevels();
if (!empty($sublevels)) {
	foreach($sublevels as $p) {
		echo '<span class="swp-breadcrumbs-level"> <span class="delim">'. htmlspecialchars($delimiter) .'</span> ';
		if ($p["link"] !== false) {
			echo '<a href="'. $p["link"] .'">';
		} else {
			echo '<strong>';
		}
		echo $p["title"];
		if ($p["link"] !== false) {
			echo '</a>';
		} else {
			echo '</strong>';
		}
		echo '</span>';
	}
}
?>
</div>