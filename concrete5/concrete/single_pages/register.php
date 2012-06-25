<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="page-header">
	<h1><?php echo t('Site Registration')?></h1>
</div>
<div class="ccm-form">

<?php  
if($success) { 
	switch($success) { 
		case "registered": 
			?>
			<p><strong><?php echo $successMsg ?></strong><br/><br/>
			<a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?></a>
			<?php  
		break;
		case "validate": 
			?>
			<p><?php echo $successMsg[0] ?></p>
			<p><?php echo $successMsg[1] ?></p>
			<p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?></a></p>
			<?php 
		break;
		case "pending":
			?>
			<p><?php echo $successMsg ?></p>
			<p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?></a></p>
            <?php 
		break;
	}
		
} else { ?>

<form method="post" action="<?php echo $this->url('/register', 'do_register')?>">
<div class="row">
<div class="span8 columns">
	<fieldset>
		<legend><?php echo t('Your Details')?></legend>
		<?php  if ($displayUserName) { ?>
			<div class="clearfix">
				<?php echo  $form->label('uName',t('Username')); ?>
				<div class="input">
					<?php echo  $form->text('uName'); ?>
				</div>
			</div>
		<?php  } ?>
	
		<div class="clearfix">
			<?php  echo $form->label('uEmail',t('Email Address')); ?>
			<div class="input">
				<?php  echo $form->text('uEmail'); ?>
			</div>
		</div>
		<div class="clearfix">
			<?php  echo $form->label('uPassword',t('Password')); ?>
			<div class="input">
				<?php  echo $form->password('uPassword'); ?>
			</div>
		</div>
		<div class="clearfix">
			<?php  echo $form->label('uPasswordConfirm',t('Confirm Password')); ?>
			<div class="input">
				<?php  echo $form->password('uPasswordConfirm'); ?>
			</div>
		</div>

	</fieldset>
</div>
<div class="span8 columns">
	<fieldset>
		<legend><?php echo t('Options')?></legend>
	<?php 
	
	$attribs = UserAttributeKey::getRegistrationList();
	$af = Loader::helper('form/attribute');
	
	foreach($attribs as $ak) { ?> 
			<?php echo  $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	?>
	<?php  }?>
	</fieldset>
</div>
<div class="span16 columns ">
	<?php  if (ENABLE_REGISTRATION_CAPTCHA) { ?>
	
		<div class="clearfix">
			<?php  $captcha = Loader::helper('validation/captcha'); ?>			
			<?php echo $captcha->label()?>
			<div class="input">
			<?php 
		  	  $captcha->showInput(); 
			  $captcha->display();
		  	  ?>
			</div>
		</div>
	
		
	<?php  } ?>

</div>
<div class="span16 columns">
	<div class="actions">
	<?php echo $form->hidden('rcID', $rcID); ?>
	<?php echo $form->submit('register', t('Register') . ' &gt;', array('class' => 'primary'))?>
	</div>
</div>
	
</div>
</form>
<?php  } ?>

</div>