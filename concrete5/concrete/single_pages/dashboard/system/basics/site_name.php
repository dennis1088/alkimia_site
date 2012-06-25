<?php  defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Site Name'), false, 'span12 offset2', false)?>
<form method="post" id="site-form" action="<?php echo $this->action('update_sitename')?>">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('update_sitename')?>
	<div class="clearfix">
	<?php echo $form->label('SITE', t('Site Name'))?>
	<div class="input">
	<?php echo $form->text('SITE', $site, array('class' => 'span8'))?>
	</div>
	</div>
</div>
<div class="ccm-pane-footer">
	<?php 
	print $interface->submit(t('Save'), 'site-form', 'right','primary');
	?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
