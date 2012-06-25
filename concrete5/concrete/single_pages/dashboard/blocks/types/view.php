<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<div class="row">
<div class="span12 offset2 columns">
<div class="ccm-pane">
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('Block Types'), t('Add custom block types, refresh the database tables of installed blocks, and uninstall blocks types from here.'));?>
<?php  if ($this->controller->getTask() == 'inspect' || $this->controller->getTask() == 'refresh') { ?>

<div class="ccm-pane-body">
	
	<h3><img src="<?php echo $ci->getBlockTypeIconURL($bt)?>" /> <?php echo $bt->getBlockTypeName()?></h3>
		
	<h5><?php echo t('Description')?></h5>
	<p><?php echo $bt->getBlockTypeDescription()?></p>

	<h5><?php echo t('Usage Count')?></h5>
	<p><?php echo $num?></p>
		
	<?php  if ($bt->isBlockTypeInternal()) { ?>
	<h5><?php echo t('Internal')?></h5>
	<p><?php echo t('This is an internal block type.')?></p>
	<?php  } ?>

</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/blocks/types')?>" class="btn"><?php echo t('Back to Block Types')?></a>

	<?php  print $ch->button(t("Refresh"), $this->url('/dashboard/blocks/types','refresh', $bt->getBlockTypeID()), "right"); ?>
	<?php 
	$u = new User();
	if ($u->isSuperUser()) {
	
		$removeBTConfirm = t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle());
		
		print $ch->button_js(t('Remove'), 'removeBlockType()', 'right', 'error');?>

		<script type="text/javascript">
		removeBlockType = function() {
			if (confirm('<?php echo $removeBTConfirm?>')) { 
				location.href = "<?php echo $this->url('/dashboard/blocks/types', 'uninstall', $bt->getBlockTypeID(), $valt->generate('uninstall'))?>";				
			}
		}
		</script>

	<?php  } else { ?>
		<?php  print $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove block types.') . '\')', 'right', 'disabled error');?>
	<?php  } ?>
		
</div>

<?php  } else { ?>

<div class="ccm-pane-body ccm-pane-body-footer">

	<h3><?php echo t('Custom Block Types')?></h3>
	<h5><?php echo t('Awaiting Installation')?></h5>
	<?php  if (count($availableBlockTypes) > 0) { ?>
		<ul id="ccm-block-type-list">
		<?php 	foreach ($availableBlockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
	?>
			<li class="ccm-block-type ccm-block-type-available">
				<p style="background-image: url(<?php echo $btIcon?>)" class="ccm-block-type-inner"><?php echo $ch->button(t("Install"), $this->url('/dashboard/blocks/types','install', $bt->getBlockTypeHandle()), "right", 'small');?> <?php echo $bt->getBlockTypeName()?></p>
			</li>
		<?php  } ?>
		</ul>
	
	<?php  } else { ?>
		<p><?php echo t('No custom block types are awaiting installation.')?></p>
	<?php  } ?>

	<h5><?php echo t('Currently Installed')?></h5>
	<?php  if (count($webBlockTypes) > 0) { ?>
		<ul id="ccm-block-type-list">
			<?php  foreach($webBlockTypes as $bt) { 
				$btIcon = $ci->getBlockTypeIconURL($bt);
				?>	
				<li class="ccm-block-type ccm-block-type-available">
					<a style="background-image: url(<?php echo $btIcon?>)" class="ccm-block-type-inner" href="<?php echo $this->action('inspect', $bt->getBlockTypeID())?>"><?php echo $bt->getBlockTypeName()?></a>
					<div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID()?>"><?php echo $bt->getBlockTypeDescription()?></div>
				</li>
			<?php  } ?>
		</ul>
	<?php  } else { ?>
		<p><?php echo t('No custom block types are installed.')?></p>
	<?php  } ?>
	

    <?php  if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>

	
	<div class="well" style="padding:10px 20px;">
        <h3><?php echo t('More Blocks')?></h3>
        <p><?php echo t('Browse our marketplace of add-ons to extend your site!')?></p>
        <p><a class="btn success" href="<?php echo $this->url('/dashboard/extend/add-ons')?>"><?php echo t("More Add-ons")?></a></p>
    </div>
        
    <?php  } ?>
    
	<h3><?php echo t('Core Block Types')?></h3>
		<ul id="ccm-block-type-list">
			<?php  foreach($coreBlockTypes as $bt) { 
				$btIcon = $ci->getBlockTypeIconURL($bt);
				?>	
				<li class="ccm-block-type ccm-block-type-available">
					<a style="background-image: url(<?php echo $btIcon?>)" class="ccm-block-type-inner" href="<?php echo $this->action('inspect', $bt->getBlockTypeID())?>"><?php echo $bt->getBlockTypeName()?></a>
					<div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID()?>"><?php echo $bt->getBlockTypeDescription()?></div>
				</li>
			<?php  } ?>
		</ul>
	



</div>
	
<?php  } ?>
</div>
</div>
</div>
</div>