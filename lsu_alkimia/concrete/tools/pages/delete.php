<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

if ($_POST['task'] == 'delete_pages') {
	$json['error'] = false;
	
	if (is_array($_POST['cID'])) {
		foreach($_POST['cID'] as $cID) {
			$c = Page::getByID($cID);
			$cp = new Permissions($c);
			$children = $c->getNumChildren();
			if ($children == 0 || $cp->canAdminPage()) {
				$c->markPendingAction('DELETE');
				if ($cp->canApproveCollection()) {
					$c->delete();
				}
			} else {
				$json['error'] = t('Unable to delete one or more pages.');
			}
		}
	}

	$js = Loader::helper('json');
	print $js->encode($json);
	exit;
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$pages[] = Page::getByID($cID);
	}
} else {
	$pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
foreach($pages as $c) { 
	$cp = new Permissions($c);
	if ($cp->canDeleteCollection()) {
		$pcnt++;
	}
}

$searchInstance = $_REQUEST['searchInstance'];

?>
<div class="ccm-ui">

<?php  if ($pcnt == 0) { ?>
	<?php echo t("You do not have permission to delete any of the selected pages."); ?>
<?php  } else { ?>

	<?php echo t('Are you sure you want to delete the following pages?')?><br/><br/>

	<form id="ccm-<?php echo $searchInstance?>-delete-form" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/delete">
	<?php echo $form->hidden('task', 'delete_pages')?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="zebra-striped">
	<tr>
		<th><?php echo t('Name')?></th>
		<th><?php echo t('Page Type')?></th>
		<th><?php echo t('Date Added')?></th>
		<th><?php echo t('Author')?></th>
	</tr>
	
	<?php  foreach($pages as $c) { 
		$cp = new Permissions($c);
		$c->loadVersionObject();
		if ($cp->canDeleteCollection()) { ?>
		
		<?php echo $form->hidden('cID[]', $c->getCollectionID())?>		
		
		<tr>
			<td class="ccm-page-list-name"><?php echo $c->getCollectionName()?></td>
			<td><?php echo $c->getCollectionTypeName()?></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDatePublic()))?></td>
			<td><?php 
				$ui = UserInfo::getByID($c->getCollectionUserID());
				if (is_object($ui)) {
					print $ui->getUserName();
				}
			}?></td>
		
		</tr>
		
		<?php  }  ?>
	</table>
	</form>
	<div class="dialog-buttons">
	<?php  $ih = Loader::helper('concrete/interface')?>
	<?php echo $ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
	<?php echo $ih->button_js(t('Delete'), 'ccm_sitemapDeletePages(\'' . $searchInstance . '\')', 'right', 'btn error')?>
	</div>		
		
	<?php 
	
}
?>
</div>