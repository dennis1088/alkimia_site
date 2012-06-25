<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class TestFormExternalFormBlockController  {

	function __construct(&$controller) {
		$this->controller = $controller;
	}
	
	public function on_before_submit() {
		/*
			This is just an example on how you'd add more validation rules to your form
			Suppose 3 is the email address
		*/
		
		/*
		if (!some_function_to_validate_emails($_REQUEST['Question3'])) { 
			$this->controller->errors['email'] = t('Please enter a valid email address');
		}
		*/
	}
	
	public function on_after_submit() {
		/*
			This is just an example on how you'd submit the form to campaignmonitor after it's been processed by c5
			For the example, suppose 1 is firstame, 2 is last name, 3 is email
		*/
		
		/*
		$post = array();
		$post['cm-name'] = $this->controller->questionAnswerPairs['1']['answer'].' '.$this->controller->questionAnswerPairs['2']['answer'];
		$post['cm-itutkr-itutkr'] = $this->controller->questionAnswerPairs['3']['answer'];
					
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_URL, 'http://url.to.my.campaignmonitor/myform');
		//Don't ask me what this does, I just know that without this funny header, the whole thing doesn't work!
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Expect:'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POST, 1 );
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
		
		$url = curl_exec( $ch );
		curl_close ($ch);
		*/
	}
	
}