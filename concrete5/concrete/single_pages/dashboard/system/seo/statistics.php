<?php  defined('C5_EXECUTE') or die('Access Denied');
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Statistics'), t("Tracks page views in concrete5. Disabling this may increase site performance, but you will have to get statistics information from elsewhere."), 'span7 offset5', false); ?>

	<form method="post" class="form-stacked" id="url-form" action="<?php  echo $this->action('')?>">
		<?php echo $this->controller->token->output('update_statistics')?>
		<div class="ccm-pane-body">
			<div class="clearfix">
				<label></label>
				<div class="input">
				<ul class="inputs-list">
				<li>
				<label>
				<?php echo $form->checkbox('STATISTICS_TRACK_PAGE_VIEWS', 1, STATISTICS_TRACK_PAGE_VIEWS); ?>
				<span><?php echo t('Track page view statistics.');?></span>
				</label>
				</li>
				</ul>
				</div>
			</div>
		</div>
		<div class="ccm-pane-footer">
			<?php  echo $interface->submit(t('Save'), null, 'right', 'primary');?>
		</div>
	</form>

<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();
