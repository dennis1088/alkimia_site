<?php  
defined('C5_EXECUTE') or die("Access Denied.");
?>

<br/>
	<?php 
	if ($cp->canAdminPage() && is_object($a)) {
		$ax = $a;
		if ($a->isGlobalArea()) {
			$cx = Stack::getByName($a->getAreaHandle());
			$a = Area::get($cx, STACKS_AREA_NAME);
		}
		$btArray = BlockTypeList::getAreaBlockTypes($a, $cp);
		
		
		// if the area overrides the collection permissions explicitly (with a one on the override column) we check
		
		if ($a->overrideCollectionPermissions()) {
			$gl = new GroupList($a);
			$ul = new UserInfoList($a);
		} else {
			// now it gets more complicated. 
			$permsSet = false;
			
			if ($a->getAreaCollectionInheritID() > 0) {
				// in theory we're supposed to be inheriting some permissions from an area with the same handle,
				// set on the collection id specified above (inheritid). however, if someone's come along and
				// reverted that area to the page's permissions, there won't be any permissions, and we 
				// won't see anything. so we have to check
				$areac = Page::getByID($a->getAreaCollectionInheritID());
				$inheritArea = Area::get($areac, $_GET['arHandle']);
				if ($inheritArea->overrideCollectionPermissions()) {
					// okay, so that area is still around, still has set permissions on it. So we
					// pass our current area to our grouplist, userinfolist objects, knowing that they will 
					// smartly inherit the correct items.
					$gl = new GroupList($a);
					$ul = new UserInfoList($a);
					$permsSet = true;				
				}
			}		
			
			if (!$permsSet) {
				// otherwise we grab the collection permissions for this page
				$gl = new GroupList($c);
				$ul = new UserInfoList($c);
			}	
		}
		
		$gArray = $gl->getGroupList();
		$ulArray = $ul->getUserInfoList();
		
		?>
		<script type="text/javascript">
			function ccm_triggerSelectUser(uID, uName) {
				rowValue = "uID:" + uID;
				rowText = uName;
				if ($("#_row_uID_" + uID).length > 0) {
					return false;
				}			
	
				tbl = document.getElementById("ccmPermissionsTableArea");	   
				row1 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row1.id = "_row_uID_" + uID;
				row2 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row3 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?    
				
				row1.className = rowValue.replace(":","_");
				row2.className = rowValue.replace(":","_");
				row3.className = rowValue.replace(":","_");
				
				row1Cell = document.createElement("TD");
				row1Cell.innerHTML = '<h4><a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + rowText + "</h4>";
				row1Cell.colSpan = 7;
				row1Cell.className =  'ccm-permissions-header';
				row1.appendChild(row1Cell);
				
				row2Cell1 = row2.insertCell(0);
				row2Cell2 = row2.insertCell(1);
				row2Cell3 = row2.insertCell(2);
				row2Cell4 = row2.insertCell(3);
				row2Cell5 = row2.insertCell(4);
				row2Cell6 = row2.insertCell(5);
				row2Cell7 = row2.insertCell(6);
				
				row2Cell1.vAlign = 'top';
				row2Cell2.vAlign = 'top';
				row2Cell3.vAlign = 'top';
				row2Cell4.vAlign = 'top';
				row2Cell5.vAlign = 'top';
				row2Cell6.vAlign = 'top';
				row2Cell7.width = '100%';
				
				row2Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Read')?></strong></div>';
				row2Cell2.innerHTML = '<input type="checkbox" name="areaRead[]" value="' + rowValue + '">';
				row2Cell3.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div>';
				row2Cell4.innerHTML = '<input type="checkbox" name="areaEdit[]" value="' + rowValue + '" />';
				row2Cell5.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div>';
				row2Cell6.innerHTML = '<input type="checkbox" name="areaDelete[]" value="' + rowValue + '" />';
				row2Cell7.innerHTML = '<div style="width: 225px">&nbsp;</div>';
				
				row3Cell1 = row3.insertCell(0);
				row3Cell1.vAlign = 'top';
				row3Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Add')?></strong></div>';
				row3Cell2 = row3.insertCell(1);
				row3Cell2.colSpan = 7;
				row3Cell2.vAlign = 'top';
				row3Cell2.width = '100%';
				row3Cell2.innerHTML = '<div style="width: 460px;">';
				<?php  foreach ($btArray as $bt) { ?>
					row3Cell2.innerHTML += '<div style="white-space: nowrap; float: left; width: 130px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?php echo htmlspecialchars($bt->getBlockTypeName(), ENT_QUOTES, APP_CHARSET)?></div>';
				<?php  } ?>		
				row3Cell2.innerHTML += '</div>';
			}
			
			function ccm_triggerSelectGroup(gID, gName) {
				// we add a row for the selected group
				var rowText = gName;
				var rowValue = "gID:" + gID;
				
				if ($("#_row_gID_" + gID).length > 0) {
					return false;
				}
				
				tbl = document.getElementById("ccmPermissionsTableArea");	   
				row1 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row1.id = "_row_gID_" + gID;
				row2 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row3 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?    
				
				row1.className = rowValue.replace(":","_");
				row2.className = rowValue.replace(":","_");
				row3.className = rowValue.replace(":","_");
				
				row1Cell = document.createElement("TD");
				row1Cell.innerHTML = '<h4><a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + rowText + '</h4>';
				row1Cell.colSpan = 7;
				row1Cell.className =  'ccm-permissions-header';
				row1.appendChild(row1Cell);
				
				row2Cell1 = row2.insertCell(0);
				row2Cell2 = row2.insertCell(1);
				row2Cell3 = row2.insertCell(2);
				row2Cell4 = row2.insertCell(3);
				row2Cell5 = row2.insertCell(4);
				row2Cell6 = row2.insertCell(5);
				row2Cell7 = row2.insertCell(6);
				
				row2Cell1.vAlign = 'top';
				row2Cell2.vAlign = 'top';
				row2Cell3.vAlign = 'top';
				row2Cell4.vAlign = 'top';
				row2Cell5.vAlign = 'top';
				row2Cell6.vAlign = 'top';
				row2Cell7.width = '100%';
				
				row2Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Read')?></strong></div>';
				row2Cell2.innerHTML = '<input type="checkbox" name="areaRead[]" value="' + rowValue + '">';
				row2Cell3.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div>';
				row2Cell4.innerHTML = '<input type="checkbox" name="areaEdit[]" value="' + rowValue + '" />';
				row2Cell5.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div>';
				row2Cell6.innerHTML = '<input type="checkbox" name="areaDelete[]" value="' + rowValue + '" />';
				row2Cell7.innerHTML = '<div style="width: 225px">&nbsp;</div>';
				
				row3Cell1 = row3.insertCell(0);
				row3Cell1.vAlign = 'top';
				row3Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Add')?></strong></div>';
				row3Cell2 = row3.insertCell(1);
				row3Cell2.colSpan = 7;
				row3Cell2.vAlign = 'top';
				row3Cell2.width = '100%';
				row3Cell2.innerHTML = '<div style="width: 460px;">';
				<?php  foreach ($btArray as $bt) { ?>
					row3Cell2.innerHTML += '<div style="white-space: nowrap; float: left; width: 130px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?php echo htmlspecialchars($bt->getBlockTypeName(), ENT_QUOTES, APP_CHARSET)?></div>';
				<?php  } ?>		
				row3Cell2.innerHTML += '</div>';
				
			}
			
			function removePermissionRow(rowID, origText) {
				$("." + rowID + " input[type=checkbox]").each(function() {
					this.checked = false;
				});
				$("." + rowID).hide();
	
			  if (rowID && origText) {
				  select = document.getElementById("groupSelect");
				  currentLength = select.options.length;
				  optionDivider = select.options[currentLength - 2];
				  optionAddUser = select.options[currentLength - 1];
		
				  select.options[currentLength - 2] = new Option(origText, rowID.replace('_',':'));
				  select.options[currentLength - 1] = optionDivider;
				  select.options[currentLength] = optionAddUser;
			  }
	
			}
			
			function setPermissionAvailability(value) {
				tbl = document.getElementById("ccmPermissionsTableArea");
				switch(value) {
					case "OVERRIDE":
						inputs = tbl.getElementsByTagName("INPUT");
						for (i = 0; i < inputs.length; i++) {
							inputs[i].disabled = false;
						}
						document.getElementById("groupSelect").disabled = false;
						break;
					default:
						inputs = tbl.getElementsByTagName("INPUT");
						for (i = 0; i < inputs.length; i++) {
							inputs[i].disabled = true;
						}
						document.getElementById("groupSelect").disabled = true;
						break;
				}
			}
			
		</script>
		
<div class="ccm-ui">

<form method="post" name="permissionForm" action="<?php echo $ax->getAreaUpdateAction()?>">
	<?php  
	if ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
		$pc = $c->getPermissionsCollectionObject(); 
		$areac = Page::getByID($a->getAreaCollectionInheritID());
		?>
		

		<div class="block-message alert-message notice">
		<p>
		<?php echo t("The following area permissions are inherited from an area set on ")?>
		<a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $areac->getCollectionID()?>"><?php echo $areac->getCollectionName()?></a>. 
		<?php echo t("To change them everywhere, edit this area on that page. To override them here and on all sub-pages, edit below.")?>
		</p>
		</div>
		
<?php  	} else if (!$a->overrideCollectionPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?php echo t("The following area permissions are inherited from the page's permissions. To override them, edit below.")?>
	</p>
	</div>
	
<?php  } else { ?>

	<div class="block-message alert-message notice">
	<p><?php echo t("Permissions for this area currently override those of the page. To revert to the page's permissions, click <strong>revert to page permissions</strong> below.")?></p>
	</div>

<?php  } ?>
	
	<div class="clearfix">
	<a class="btn ccm-button-right dialog-launch" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?php echo $_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?php echo t('Add User')?>"  dialog-height="70%"><?php echo t('Add User')?></a>
	<a class="btn ccm-button-right dialog-launch" style="margin-right: 5px" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?php echo $_REQUEST['cID']?>" dialog-modal="false" dialog-title="<?php echo t('Add Group')?>"><?php echo t('Add Group')?></a>
	</div>
	
	<table id="ccmPermissionsTableArea" border="0" cellspacing="0" cellpadding="0" style="width: 100%">
		<?php  
		
		$rowNum = 1;
		foreach ($gArray as $g) { 
			$displayRow = false;
			$display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAddBlocks() || $g->canDeleteBlock()) 
			? true : false;
			
			if ($display) { ?>
	
				<tr class="gID_<?php echo $g->getGroupID()?>" id="_row_gID_<?php echo $g->getGroupID()?>">
				<td colspan="7" style="text-align: left; white-space: nowrap"><?php  if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
							<a href="javascript:removePermissionRow('gID_<?php echo $g->getGroupID()?>', '<?php echo $g->getGroupName()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>
						<?php  } ?>
						<h4><?php echo $g->getGroupName()?></h4></td>		
				</tr>
				<tr class="gID_<?php echo $g->getGroupID()?>">
				<td valign="top" style="text-align: center"><strong><?php echo t('Read')?></strong></td>
				<td valign="top" ><input type="checkbox" name="areaRead[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canRead()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div></td>
				<td><input type="checkbox" name="areaEdit[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canWrite()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div></td>
				<td><input type="checkbox" name="areaDelete[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canDeleteBlock()) { ?> checked<?php  } ?>></td>
				<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
				</tr>
				<tr class="gID_<?php echo $g->getGroupID()?>">
				<td valign="top"  style="text-align: center"><strong><?php echo t('Add')?></strong></td>
				<td colspan="6" width="100%">
				<div style="width: 460px;">
					<?php  foreach ($btArray as $bt) { ?>
						<span style="white-space: nowrap; float: left; width: 130px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($bt->canAddBlock($g)) { ?> checked<?php  } ?>>&nbsp;<?php echo $bt->getBlockTypeName()?></span>		
					<?php  } ?>
				</div>
				</td>
				</tr>

			<?php  
				$rowNum++;
			} ?>
	<?php   }
		
		foreach ($ulArray as $ui) { ?>
	
			<tr id="_row_uID_<?php echo $ui->getUserID()?>" class="uID_<?php echo $ui->getUserID()?> no-bg">
				<td colspan="7" style="text-align: left; white-space: nowrap">
					<a href="javascript:removePermissionRow('uID_<?php echo $ui->getUserID()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right"></a>
					<h4><?php echo $ui->getUserName()?></h4>
				</td>
			</tr>
			<tr class="uID_<?php echo $ui->getUserID()?>" >
				<td valign="top" style="text-align: center"><strong><?php echo t('Read')?></strong></td>
				<td valign="top" ><input type="checkbox" name="areaRead[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canRead()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div></td>
				<td><input type="checkbox" name="areaEdit[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canWrite()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div></td>
				<td><input type="checkbox" name="areaDelete[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canDeleteBlock()) { ?> checked<?php  } ?>></td>
				<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
			</tr>
			<tr class="uID_<?php echo $ui->getUserID()?>" >
				<td valign="top"  style="text-align: center"><strong><?php echo t('Add')?></strong></td>
				<td colspan="6" width="100%">
				<div style="width: 460px;">
					<?php  foreach ($btArray as $bt) { ?>
						<span style="white-space: nowrap; float: left; width: 130px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($bt->canAddBlock($ui)) { ?> checked<?php  } ?>>&nbsp;<?php echo $bt->getBlockTypeName()?></span>
					<?php  } ?>
				</div>
				</td>
			</tr>
		
			<?php  
			$rowNum++;
		} ?>
	
	</table>
	
	<input type="hidden" name="aRevertToPagePermissions" id="aRevertToPagePermissions" value="0" />

	<div class="dialog-buttons">
	<?php  if ($a->overrideCollectionPermissions()) { ?>
		<a href="javascript:void(0)" onclick="$('#aRevertToPagePermissions').val(1);$('form[name=permissionForm]').get(0).submit()" class="ccm-button-left btn"><?php echo t('Revert to Page Permissions')?></a>
	<?php  } ?>
		<a href="javascript:void(0)" onclick="$('form[name=permissionForm]').get(0).submit()" class="ccm-button-right accept primary btn"><?php echo t('Update')?></a>
	</div>

</form>
</div>

<?php  } ?>
