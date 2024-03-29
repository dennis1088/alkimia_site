<?php
// Cache directories
$this->css_dir( REL_DIR_FILES_CACHE.'/css/' );
$this->js_dir ( REL_DIR_FILES_CACHE.'/js/' );	

// Organisational lists
$this->keys_no_file		( array ('tiny_mce.js','ccm.app.js','jquery.ui.js') );	
$this->keys_ignore 		( array ('tony_tracker','recaptcha') );
$this->keys_top_footer	( array ('var CCM_') );	

if (version_compare(APP_VERSION,'5.4.2','>=')){
		
		$Found = FALSE;
		$CDN_jq = $this->CDNS->CDN('jquery.js');
		$CDN_jq_min = $this->CDNS->CDN('jquery.min.js');
		$CDN_jq_ui = $this->CDNS->CDN('jquery.ui.js');
		
		
		if (version_compare(APP_VERSION,'5.5','<')){	// 5.5 uses jquery 1.7.0
			// Concrete 5.4.2.1, 5.4.2.2
			if (is_object($CDN_jq) && strpos($CDN_jq->Tag(),'1.6.2') === FALSE){
				$this->CDNS->Delete($CDN_jq);
				$this->CDNS->Add('jquery.js','FOOT',0,	'ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
				$Found = TRUE;
			}
			if (is_object($CDN_jq_min) && strpos($CDN_jq_min->Tag(),'1.6.2') === FALSE){
				$this->CDNS->Delete($CDN_jq_min);
				$this->CDNS->Add('jquery.min.js','FOOT',0,	'ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
				$Found = TRUE;
			} 
		} else {
			// Concrete 5.5+
			if (is_object($CDN_jq) && strpos($CDN_jq->Tag(),'1.7.1') === FALSE){
				$this->CDNS->Delete($CDN_jq);
				$this->CDNS->Add('jquery.js','FOOT',0,	'ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
				$Found = TRUE;
				
			}
			
			if (is_object($CDN_jq_min) && strpos($CDN_jq_min->Tag(),'1.7.1') === FALSE){
				$this->CDNS->Delete($CDN_jq_min);
				$this->CDNS->Add('jquery.min.js','FOOT',0,	'ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
				$Found = TRUE;
		
			}
		}
		
		if (is_object($CDN_jq_ui) && strpos($CDN_jq_ui->Tag(),'1.8.16') === FALSE){
				$this->CDNS->Delete($CDN_jq_ui);
				$this->CDNS->Add('jquery.ui.js','FOOT',2,	'ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
				$Found = TRUE;
		}
		if ($Found){
			$this->CDNS->Clear_Cache();
		}
}
// Admin mode hook
// Prevent moving of analytics when in Admin mode (work-around)	
$u = new User();
if ($u->isSuperUser())  {
		$this->keys_ignore($this->keys_ga());
		$this->minify_html(2); // minifier contention - use old method						
}
// CMS sepcific file location
// find the CSS and javascript files
function find_it($path){
	if ((stripos($path,'index.php') !== FALSE)){
		$h = Loader::helper('html');
		$p = explode('/',$path);
		$fname= array_pop($p);
	
		if (stripos($fname,'.js') !== FALSE) {$o = $h->javascript($fname); $path = $o->file;}
		else 
		if (stripos($fname,'.css') !== FALSE) {$o = $h->css($fname); $path = $o->file;}
	}
	return $path;
}
?>