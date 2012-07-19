<?php 
defined('C5_EXECUTE') or die("Access Denied.");
header('Content-type: text/javascript');?>

var menuHTML = '';

<?php 
if ($_REQUEST['cvID'] > 0) {
	$c = Page::getByID($_REQUEST['cID'], $_REQUEST['cvID']);
} else {
	$c = Page::getByID($_REQUEST['cID']);
}
$cp = new Permissions($c);
$req = Request::get();
$req->setCurrentPage($c);

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/interface');
$token = '&' . $valt->getParameter();

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName());
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= t("This page is an alias of one that actually appears elsewhere. ");
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "'>" . t('View/Edit Original') . "</a>";
		if ($cp->canApproveCollection()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token . "'>" . t('Remove Alias') . "</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= t("This page is pending approval.");
				if ($cp->canApproveCollection() && !$c->isCheckedOut()) {
					$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token . "'>" . t('Approve Version') . "</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= t("This page is being moved.");
			if ($cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>" . t('Approve Move') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= t("This page is marked for removal.");
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " " . t('This will remove %s pages.', $pages);
				if ($cp->canAdminPage()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
				} else {
					$statusMessage .= " " . t('Only administrators can approve a multi-page delete operation.');
				}
			} else if ($children == 0 && $cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		}
	
	}

	if ($c->isMasterCollection()) {
		$statusMessage .= $statusMessage ? "<br/>" : "";
		$statusMessage .= t('Page Defaults for') . ' "' . $c->getCollectionTypeName() . '" ' . t("page type");
		$statusMessage .= "<br/>" . t('(All edits take effect immediately)');
	}

	if ($dh->canRead() || $cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) { 
	
		$cID = $c->getCollectionID(); ?>





menuHTML += '<div id="ccm-page-controls-wrapper" class="ccm-ui">';
menuHTML += '<div id="ccm-toolbar">';

menuHTML += '<ul id="ccm-main-nav">';
menuHTML += '<li id="ccm-logo-wrapper"><?php echo Loader::helper('concrete/interface')->getToolbarLogoSRC()?></li>';

<?php  if ($c->isMasterCollection()) { ?>
	menuHTML += '<li><a class="ccm-icon-back ccm-menu-icon" href="<?php echo View::url('/dashboard/pages/types')?>"><?php echo t('Page Types')?></a></li>';
<?php  } ?>

<?php  if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) { ?>
	
	menuHTML += '<li <?php  if ($c->isEditMode()) { ?>class="ccm-nav-edit-mode-active"<?php  } ?>><a class="ccm-icon-edit ccm-menu-icon" id="ccm-nav-edit" href="<?php  if (!$c->isEditMode()) { ?><?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-out<?php echo $token?><?php  } else { ?>javascript:void(0);<?php  } ?>"><?php  if ($c->isEditMode()) { ?><?php echo t('Editing')?><?php  } else { ?><?php echo t('Edit')?><?php  } ?></a></li>';
	<?php 
	$items = $ihm->getPageHeaderMenuItems('left');
	foreach($items as $ih) {
		$cnt = $ih->getController(); 
		if ($cnt->displayItem()) {
		?>
			menuHTML += '<li><?php echo $cnt->getMenuLinkHTML()?></li>';
		<?php 
		}
	}
	
} ?>

<?php  if (Loader::helper('concrete/interface')->showWhiteLabelMessage()) { ?>
	menuHTML += '<li id="ccm-white-label-message"><?php echo t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL)?></li>';
<?php  }
?>
menuHTML += '</ul>';
menuHTML += '<ul id="ccm-system-nav">';
<?php 
$items = $ihm->getPageHeaderMenuItems('right');
foreach($items as $ih) {
	$cnt = $ih->getController(); 
	if ($cnt->displayItem()) {
	?>
		menuHTML += '<li><?php echo $cnt->getMenuLinkHTML()?></li>';
	<?php 
	}
}
?>

<?php  if ($dh->canRead()) { ?>
	menuHTML += '<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard" href="<?php echo View::url('/dashboard')?>"><?php echo t('Dashboard')?></a></li>';
<?php  } ?>
menuHTML += '<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?php echo t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>';
menuHTML += '<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?php echo View::url('/login', 'logout')?>"><?php echo t('Sign Out')?></a></li>';
menuHTML += '</ul>';

menuHTML += '</div>';

<?php 
$dh = Loader::helper('concrete/dashboard');
?>

menuHTML += '<?php echo addslashes($dh->getDashboardAndSearchMenus())?>';

menuHTML += '<div id="ccm-edit-overlay">';
menuHTML += '<div class="ccm-edit-overlay-inner">';

<?php  if ($c->isEditMode()) { ?>

menuHTML += '<div id="ccm-exit-edit-mode-direct" <?php  if ($vo->isNew()) { ?>style="display: none"<?php  } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions">';
menuHTML += '<a href="javascript:void(0)" onclick="window.location.href=\'<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-in<?php echo $token?>\'" id="ccm-nav-exit-edit-direct" class="btn primary"><?php echo t('Exit Edit Mode')?></a>';
menuHTML += '</div>';
menuHTML += '<span class="label notice"><?php echo t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?php echo t('Page currently in edit mode on %s', date(DATE_APP_GENERIC_MDYT))?>';

menuHTML += '</div>';

menuHTML += '<div id="ccm-exit-edit-mode-comment" <?php  if (!$vo->isNew()) { ?>style="display: none"<?php  } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions clearfix">';
menuHTML += '<form method="post" id="ccm-check-in" action="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-in">';
<?php  $valt = Loader::helper('validation/token'); ?>
menuHTML += '<?php echo $valt->output('', true)?>';
menuHTML += '<h4><?php echo t('Version Comments')?></h4>';
menuHTML += '<p><input type="text" name="comments" id="ccm-check-in-comments" value="<?php echo addslashes($vo->getVersionComments())?>" onclick="this.select()" style="width:520px"/></p>';
<?php  if ($cp->canApproveCollection()) { ?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-publish" class="btn primary" style="float: right"><span><?php echo t('Publish My Edits')?></span></a>';
<?php  } ?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-preview" class="btn" style="float: right"><span><?php echo t('Preview My Edits')?></span></a>';
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-discard" class="btn" style="float: left"><span><?php echo t('Discard My Edits')?></span></a>';
menuHTML += '<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />';
menuHTML += '</form><br/>';

menuHTML += '</div>';
menuHTML += '<span class="label notice"><?php echo t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?php echo t('Page currently in edit mode on %s', date(DATE_APP_GENERIC_MDYT))?>';

menuHTML += '</div>';

<?php  } else { ?>

menuHTML += '<div class="ccm-edit-overlay-actions">';
<?php  if ($cp->canWrite()) { ?>
	menuHTML += '<a id="ccm-nav-check-out" href="<?php  if (!$cantCheckOut) { ?><?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-out<?php echo $token?><?php  } else { ?>javascript:void(0);<?php  } ?>" class="btn primary <?php  if ($cantCheckOut) { ?> disabled <?php  } ?> tooltip" <?php  if ($cantCheckOut) { ?>title="<?php echo t('Someone has already checked this page out for editing.')?>"<?php  } ?>><?php echo t('Edit this Page')?></a>';
<?php  } ?>
<?php  if ($cp->canAddSubContent()) { ?>
	menuHTML += '<a id="ccm-toolbar-add-subpage" dialog-width="645" dialog-modal="false" dialog-append-buttons="true" dialog-height="345" dialog-title="<?php echo t('Add a Sub-Page')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $cID?>&ctask=add"class="btn"><?php echo t('Add a Sub-Page')?></a>';
<?php  } ?>
menuHTML += '</div>';
menuHTML += '<span class="label notice"><?php echo t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?php echo t('Page last edited on %s', $c->getCollectionDateLastModified(DATE_APP_GENERIC_MDYT))?>';


<?php  } ?>

menuHTML += '</div>';

<?php  if (!$cantCheckOut) { ?>

menuHTML += '<div id="ccm-edit-overlay-footer">';
menuHTML += '<div class="ccm-edit-overlay-inner">';
menuHTML += '<ul>';
<?php  if ($cp->canWrite()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-properties" id="ccm-toolbar-nav-properties" dialog-width="640" dialog-height="<?php  if ($cp->canApproveCollection() && (!$c->isEditMode())) { ?>450<?php  } else { ?>390<?php  } ?>" dialog-append-buttons="true" dialog-modal="false" dialog-title="<?php echo t('Page Properties')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<?php  if ($cp->canApproveCollection() && (!$c->isEditMode())) { ?>approveImmediately=1<?php  } ?>&cID=<?php echo $c->getCollectionID()?>&ctask=edit_metadata"><?php echo t('Properties')?></a></li>';
<?php  } ?>
<?php  if ($cp->canAdminPage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-design" id="ccm-toolbar-nav-design" dialog-append-buttons="true" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="<?php echo t('Design')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $cID?>&ctask=set_theme"><?php echo t('Design')?></a></li>';
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-permissions" dialog-append-buttons="true" id="ccm-toolbar-nav-permissions" dialog-width="640" dialog-height="330" dialog-modal="false" dialog-title="<?php echo t('Permissions')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=edit_permissions"><?php echo t('Permissions')?></a></li>';
<?php  } ?>
<?php  if ($cp->canReadVersions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-versions" id="ccm-toolbar-nav-versions" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="<?php echo t('Page Versions')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>"><?php echo t('Versions')?></a></li>';
<?php  } ?>
<?php  if ($cp->canAdminPage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-speed-settings" id="ccm-toolbar-nav-speed-settings" dialog-append-buttons="true" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="<?php echo t('Speed Settings')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=edit_speed_settings"><?php echo t('Speed Settings')?></a></li>';
<?php  } ?>
<?php   if ($sh->canRead()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-move-copy" id="ccm-toolbar-nav-move-copy" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?php echo t('Move/Copy Page')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?select_mode=move_copy_delete&cID=<?php echo $cID?>"><?php echo t('Move/Copy')?></a></li>';
<?php  } ?>
<?php  if ($cp->canDeleteCollection()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-delete" dialog-append-buttons="true" id="ccm-toolbar-nav-delete" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="<?php echo t('Delete Page')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=delete"><?php echo t('Delete')?></a></li>';
<?php  } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

<?php  } ?>

menuHTML += '</div>';
menuHTML += '<?php echo addslashes($ish->getQuickNavigationBar())?>';

<?php 
/*

?>

menuHTML += '<li class="ccm-main-nav-arrange-option" <?php  if (!$c->isArrangeMode()) { ?> style="display: none" <?php  } ?>><a href="#" id="ccm-nav-save-arrange"><?php echo t('Save Positioning')?></a></li>';

menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '<div id="ccm-page-detail"><div id="ccm-page-detail-l"><div id="ccm-page-detail-r" class="ccm-ui"><div id="ccm-page-detail-content"></div></div></div>';
menuHTML += '<div id="ccm-page-detail-lower"><div id="ccm-page-detail-bl"><div id="ccm-page-detail-br"><div id="ccm-page-detail-b"></div></div></div></div>';
menuHTML += '</div>';

<?php  */ ?>

<?php 
	}
	
} ?>

<?php 
if ($statusMessage != '') {?> 
	$(function() { ccmAlert.hud('<?php echo str_replace("'",'"',$statusMessage) ?>', 5000); });
<?php  } ?>

	
$(function() {
	<?php  if ($c->isEditMode()) { ?>
		$(ccm_editInit);	
	<?php  } ?>

	<?php  
	if (!$dh->inDashboard()) { ?>
		$("#ccm-page-controls-wrapper").html(menuHTML);
		$(".tooltip").twipsy();
		ccm_activateToolbar();
	<?php  } ?>
	
	

	
});
