<p>This is the add template for the events archive. Anything you add in this view will automatically be wrapped in a form and, when submitted, sent to the block's controller.</p>

<?php echo $form->label('numentries', 'Number of entries: ');?>
<?php echo $form->text('numentries', array('style' => 'width: 50px' ));?>
<?php echo $form->label('mode', 'Select Display Mode: ');?>
<?php echo $form->select('mode', array('f' => 'Full', 't' => 'teaser'), 't');