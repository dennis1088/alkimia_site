<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
$survey=$controller;  
$miniSurvey=new CF_MiniSurvey($b);
$miniSurvey->frontEndMode=true;
$questions = $miniSurvey->getQuestions( $survey->questionSetId);
?>

<?php /* initialize form and required hidden fields */ ?> 
<form enctype="multipart/form-data" id="miniSurveyView<?php echo intval($bID)?>" class="miniSurveyView" method="post" action="<?php  echo $this->action('submit_form')?>">
	<input name="fromCID" type="hidden" value="<?php echo $_REQUEST['fromCID'] ?>" />
	<input name="qsID" type="hidden" value="<?php echo  intval($survey->questionSetId)?>" />
	<input name="pURI" type="hidden" value="<?php echo  $pURI ?>" />
	
	<?php /* display errors and messages */ ?>
	<?php  if ($invalidIP) { ?>
	<div class="ccm-error"><p><?php echo $invalidIP?></p></div>
	<?php  } ?>
	<?php   if( $_GET['surveySuccess'] && $_GET['qsid']==intval($survey->questionSetId) ){ ?>
		<div id="msg"><?php  echo $survey->thankyouMsg ?></div> 
	<?php   }elseif(strlen($formResponse)){ ?>
		<div id="msg">
			<?php  echo $formResponse ?>
			<?php  
			if(is_array($errors) && count($errors)) foreach($errors as $error){
				echo '<div class="error">'.$error.'</div>';
			} ?>
		</div>
	<?php  } ?>
	
	<?php /* display form */ ?>
	<table class="formBlockSurveyTable">
	<?php foreach ($questions as $qID => $qInfo) { ?>
		<tr>
			<?php 
				switch ($qInfo['inputType']) { 
					case "header":
						?><td colspan="2"><h3><?php echo $miniSurvey->getLabel($qInfo); ?><?php echo $miniSurvey->getRequired($qInfo); ?></h3></td><?php
					break;
					default:
						?>
						<td valign="top" class="question"><?php echo $miniSurvey->getLabel($qInfo); ?>: <?php echo $miniSurvey->getRequired($qInfo); ?></td>
						<td><?php echo $miniSurvey->loadInputType($qInfo); ?></td>
						<?php
					break;
				}
			?>
		</tr>
	<?php } ?>
	
	<?php /* don't forget to add a submit button! */ ?>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="<?php echo t('Submit')?>" /></td>
		</tr>
	</table>
	
<?php /* don't forget to end the form! */ ?>
</form>