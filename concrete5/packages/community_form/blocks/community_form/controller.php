<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class CommunityFormBlockController extends BlockController {

	public $btTable = 'btCommunityForm';
	public $btQuestionsTablename = 'btCommunityFormQuestions';
	public $btAnswerSetTablename = 'btCommunityFormAnswerSet';
	public $btAnswersTablename = 'btCommunityFormAnswers'; 	
	public $btInterfaceWidth = '420';
	public $btInterfaceHeight = '430';
	public $thankyouMsg=''; 
	
	protected $noSubmitFormRedirect=0;
	protected $lastAnswerSetId=0;
		
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Build simple forms and surveys.");
	}
	
	public function getBlockTypeName() {
		return t("Community Form");
	}
	
	public function getJavaScriptStrings() {
		return array(
			'delete-question' => t('Are you sure you want to delete this question?'),
			'form-name' => t('Your form must have a name.'),
			'complete-required' => t('Please complete all required fields.'),
			'ajax-error' => t('AJAX Error.'),
			'form-min-1' => t('Please add at least one question to your form.')			
		);
	}
	
	public function __construct($b = null){ 
		parent::__construct($b);
		//$this->bID = intval($this->_bID);
		if(!strlen($this->thankyouMsg)){ 
			$this->thankyouMsg = $this->getDefaultThankYouMsg();
		}
		if( strlen(FORM_BLOCK_SENDER_EMAIL)>1 && strstr(FORM_BLOCK_SENDER_EMAIL,'@') ){
			$this->formFormEmailAddress = FORM_BLOCK_SENDER_EMAIL;  
		}else{ 
			$adminUserInfo=UserInfo::getByID(USER_SUPER_ID);
			$this->formFormEmailAddress = $adminUserInfo->getUserEmail(); 
		}  
	}
	
	
	
	function getExternalControllers() {	
		$fh = Loader::helper('file');
		$firstdir = dirname(__FILE__);
		$controllers = array();
		$filename = "controllers";
		if (file_exists($firstdir.'/'.$filename)) {
			$controllers = array_merge($controllers, $fh->getDirectoryContents($firstdir.'/'.$filename));
		}
		if ($firstdir != DIR_FILES_BLOCK_TYPES. '/' .$this->btHandle &&  file_exists(DIR_FILES_BLOCK_TYPES . '/' .$this->btHandle.'/'.$filename)) {
			$controllers = array_merge($controllers, $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES . '/' .$this->btHandle.'/'.$filename));
		}
		$ret = array();
		foreach ($controllers as $cont) {
			$ret[$cont] = $fh->unfilename($cont);
		}
		return $ret;
	}

	public function getBlockPath($filename = null) {
		$path = false;
		$firstdir = dirname(__FILE__);
		if (file_exists($firstdir.'/'.$filename)) {
			$path = $firstdir.'/'.$filename;
		}
		elseif ($firstdir != DIR_FILES_BLOCK_TYPES. '/' .$this->btHandle &&  file_exists(DIR_FILES_BLOCK_TYPES . '/' .$this->btHandle.'/'.$filename)) {
			$path = DIR_FILES_BLOCK_TYPES . '/' .$this->btHandle.'/'.$filename;
		}
		return $path;
	}

	public function view(){ 
		$pURI = ($_REQUEST['pURI']) ? $_REQUEST['pURI'] : str_replace(array('&ccm_token='.$_REQUEST['ccm_token'],'&btask=passthru','&method=submit_form'),'',$_SERVER['REQUEST_URI']);
		$this->set('pURI',  htmlentities( $pURI, ENT_COMPAT, APP_CHARSET));
	}
	
	public function getDefaultThankYouMsg() {
		return t("Thanks!");
	}
	
	//form add or edit submit 
	//(run after the duplicate method on first block edit of new page version)
	function save( $data=array() ) { 
		if( !$data || count($data)==0 ) $data=$_POST;  
		
		$b=$this->getBlockObject(); 
		$c=$b->getBlockCollectionObject();
		
		$db = Loader::db();
		if(intval($this->bID)>0){	 
			$q = "select count(*) as total from {$this->btTable} where bID = ".intval($this->bID);
			$total = $db->getOne($q);
		}else $total = 0; 
			
		if($_POST['qsID']) $data['qsID']=$_POST['qsID'];
		if( !$data['qsID'] ) $data['qsID']=time(); 	
		if(!$data['oldQsID']) $data['oldQsID']=$data['qsID']; 
		$data['bID']=intval($this->bID); 
		
		$v = array( $data['qsID'], $data['surveyName'], intval($data['notifyMeOnSubmission']), $data['recipientEmail'], $data['thankyouMsg'], intval($data['displayCaptcha']),$data['externalController'], intval($this->bID) );
 		
		//is it new? 
		if( intval($total)==0 ){ 
			$q = "insert into {$this->btTable} (questionSetId, surveyName, notifyMeOnSubmission, recipientEmail, thankyouMsg, displayCaptcha,externalController, bID) values (?, ?, ?, ?, ?, ?,? , ?)";		
		}else{
			$q = "update {$this->btTable} set questionSetId = ?, surveyName=?, notifyMeOnSubmission=?, recipientEmail=?, thankyouMsg=?, displayCaptcha=?,externalController=? where bID = ? AND questionSetId=".$data['qsID'];
		}		
		
		$rs = $db->query($q,$v);  
		
		//Add Questions (for programmatically creating forms, such as during the site install)
		if( count($data['questions'])>0 ){
			$miniSurvey = new CF_MiniSurvey();
			foreach( $data['questions'] as $questionData )
				$miniSurvey->addEditQuestion($questionData,0);
		}
 
 		$this->questionVersioning($data);
		
		return true;
	}
	
	//Ties the new or edited questions to the new block number
	//New and edited questions are temporarily given bID=0, until the block is saved... painfully complicated
	protected function questionVersioning( $data=array() ){
		$db = Loader::db();
		$oldBID = intval($data['bID']);
		
		//if this block is being edited a second time, remove edited questions with the current bID that are pending replacement
		//if( intval($oldBID) == intval($this->bID) ){  
			$vals=array( intval($data['oldQsID']) );  
			$pendingQuestions=$db->getAll('SELECT msqID FROM btCommunityFormQuestions WHERE bID=0 && questionSetId=?',$vals); 
			foreach($pendingQuestions as $pendingQuestion){  
				$vals=array( intval($this->bID), intval($pendingQuestion['msqID']) );  
				$db->query('DELETE FROM btCommunityFormQuestions WHERE bID=? AND msqID=?',$vals);
			}
		//} 
	
		//assign any new questions the new block id 
		$vals=array( intval($data['bID']), intval($data['qsID']), intval($data['oldQsID']) );  
		$rs=$db->query('UPDATE btCommunityFormQuestions SET bID=?, questionSetId=? WHERE bID=0 && questionSetId=?',$vals);
 
 		//These are deleted or edited questions.  (edited questions have already been created with the new bID).
 		$ignoreQuestionIDsDirty=explode( ',', $data['ignoreQuestionIDs'] );
		$ignoreQuestionIDs=array(0);
		foreach($ignoreQuestionIDsDirty as $msqID)
			$ignoreQuestionIDs[]=intval($msqID);	
		$ignoreQuestionIDstr=join(',',$ignoreQuestionIDs); 
		
		//remove any questions that are pending deletion, that already have this current bID 
 		$pendingDeleteQIDsDirty=explode( ',', $data['pendingDeleteIDs'] );
		$pendingDeleteQIDs=array();
		foreach($pendingDeleteQIDsDirty as $msqID)
			$pendingDeleteQIDs[]=intval($msqID);		
		$vals=array( $this->bID, intval($data['qsID']), join(',',$pendingDeleteQIDs) );  
		$unchangedQuestions=$db->query('DELETE FROM btCommunityFormQuestions WHERE bID=? AND questionSetId=? AND msqID IN (?)',$vals);			
	} 
	
	//Duplicate will run when copying a page with a block, or editing a block for the first time within a page version (before the save).
	function duplicate($newBID) { 
	
		$b=$this->getBlockObject(); 
		$c=$b->getBlockCollectionObject();	 
		 
		$db = Loader::db();
		$v = array($this->bID);
		$q = "select * from {$this->btTable} where bID = ? LIMIT 1";
		$r = $db->query($q, $v);
		$row = $r->fetchRow();
		
		//if the same block exists in multiple collections with the same questionSetID
		if(count($row)>0){ 
			$oldQuestionSetId=$row['questionSetId']; 
			
			//It should only generate a new question set id if the block is copied to a new page,
			//otherwise it will loose all of its answer sets (from all the people who've used the form on this page)
			$questionSetCIDs=$db->getCol("SELECT distinct cID FROM {$this->btTable} AS f, CollectionVersionBlocks AS cvb ".
						"WHERE f.bID=cvb.bID AND questionSetId=".intval($row['questionSetId']) );
			
			//this question set id is used on other pages, so make a new one for this page block 
			if( count( $questionSetCIDs ) >1 || !in_array( $c->cID, $questionSetCIDs ) ){ 
				$newQuestionSetId=time(); 
				$_POST['qsID']=$newQuestionSetId; 
			}else{
				//otherwise the question set id stays the same
				$newQuestionSetId=$row['questionSetId']; 
			}
			
			//duplicate survey block record 
			//with a new Block ID and a new Question 
			$v = array($newQuestionSetId,$row['surveyName'],$newBID,$row['thankyouMsg'],intval($row['notifyMeOnSubmission']),$row['recipientEmail'],$row['displayCaptcha']);
			$q = "insert into {$this->btTable} ( questionSetId, surveyName, bID,thankyouMsg,notifyMeOnSubmission,recipientEmail,displayCaptcha) values (?, ?, ?, ?, ?, ?, ?)";
			$result=$db->Execute($q, $v); 
			
			$rs=$db->query("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=$oldQuestionSetId AND bID=".intval($this->bID) );
			while( $row=$rs->fetchRow() ){
				$v=array($newQuestionSetId,intval($row['msqID']), intval($newBID), $row['question'],$row['inputType'],$row['options'],$row['position'],$row['width'],$row['height'],$row['required']);
				$sql= "INSERT INTO {$this->btQuestionsTablename} (questionSetId,msqID,bID,question,inputType,options,position,width,height,required) VALUES (?,?,?,?,?,?,?,?,?,?)";
				$db->Execute($sql, $v);
			}
			
			return $newQuestionSetId;
		}
		return 0;	
	}
	

	//users submits the completed survey
	function action_submit_form() { 
		$this->errors = array();
		if ($this->externalController != "" && $filename = $this->getBlockPath('controllers/'.$this->externalController)) {
			$class = Object::camelcase(substr($this->externalController, 0, strrpos($this->externalController, '.php')));
			require_once($filename);
			$class .= 'ExternalFormBlockController';
			$controller = new $class($this);
		}
		if ($controller && method_exists($controller,'on_before_submit')) {
			$controller->on_before_submit();
		}
		$ip = Loader::helper('validation/ip');
		Loader::library("file/importer");
		
		if (!$ip->check()) {
			$this->set('invalidIP', $ip->getErrorMessage());			
			return;
		}	

		$txt = Loader::helper('text');
		$db = Loader::db();
		
		//question set id
		$qsID=intval($_POST['qsID']); 
		if($qsID==0)
			throw new Exception(t("Oops, something is wrong with the form you posted (it doesn't have a question set id)."));
			
		//get all questions for this question set
		$rows=$db->GetArray("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=? AND bID=?", array( $qsID, intval($this->bID)));			

		// check captcha if activated
		if ($this->displayCaptcha) {
			$captcha = Loader::helper('validation/captcha');
			if (!$captcha->check()) {
				$this->errors['captcha'] = t("Incorrect captcha code");
				$_REQUEST['ccmCaptchaCode']='';
			}
		}
		
		//checked required fields
		foreach($rows as $row){
			if( intval($row['required'])==1 ){
				$notCompleted=0;
				if($row['inputType']=='checkboxlist'){
					$answerFound=0;
					foreach($_POST as $key=>$val){
						if( strstr($key,'Question'.$row['msqID'].'_') && strlen($val) ){
							$answerFound=1;
						} 
					}
					if(!$answerFound) $notCompleted=1;
				}elseif($row['inputType']=='fileupload'){		
					if( !isset($_FILES['Question'.$row['msqID']]) || !is_uploaded_file($_FILES['Question'.$row['msqID']]['tmp_name']) )					
						$notCompleted=1;
				}elseif( !strlen(trim($_POST['Question'.$row['msqID']])) ){
					$notCompleted=1;
				}				
				if($notCompleted) $this->errors['CompleteRequired'] = t("Complete required fields *") ; 
			}
		}
		
		//try importing the file if everything else went ok	
		$tmpFileIds=array();	
		if(!count($this->errors))	foreach($rows as $row){
			if( $row['inputType']!='fileupload' ) continue;
			$questionName='Question'.$row['msqID']; 			
			if	( !intval($row['required']) && 
			   		( 
			   		!isset($_FILES[$questionName]['tmp_name']) || !is_uploaded_file($_FILES[$questionName]['tmp_name'])
			   		) 
				){
					continue;
			}
			$fi = new FileImporter();
			$resp = $fi->import($_FILES[$questionName]['tmp_name'], $_FILES[$questionName]['name']);
			if (!($resp instanceof FileVersion)) {
				switch($resp) {
					case FileImporter::E_FILE_INVALID_EXTENSION:
						$this->errors['fileupload'] = t('Invalid file extension.');
						break;
					case FileImporter::E_FILE_INVALID:
						$this->errors['fileupload'] = t('Invalid file.');
						break;
					
				}
			}else{
				$tmpFileIds[intval($row['msqID'])] = $resp->getFileID();
			}	
		}	
		
		if(count($this->errors)){			
			$this->set('formResponse', t('Please correct the following errors:') );
			$this->set('errors',$this->errors);
			$this->set('Entry',$E);			
		}else{ //no form errors			
			//save main survey record	
			$q="insert into {$this->btAnswerSetTablename} (questionSetId) values (?)";
			$db->query($q,array($qsID));
			$answerSetID=$db->Insert_ID();
			$this->lastAnswerSetId=$answerSetID;
			
			$questionAnswerPairs=array();

			//loop through each question and get the answers 
			foreach( $rows as $row ){	
				//save each answer
				if($row['inputType']=='checkboxlist'){
					$answer = Array();
					$answerLong="";
					$keys = array_keys($_POST);
					foreach ($keys as $key){
						if (strpos($key, 'Question'.$row['msqID'].'_') === 0){
							$answer[]=$txt->sanitize($_POST[$key]);
						}
					}
				}elseif($row['inputType']=='text'){
					$answerLong=$txt->sanitize($_POST['Question'.$row['msqID']]);
					$answer='';
				}elseif($row['inputType']=='fileupload'){
					 $answer=intval( $tmpFileIds[intval($row['msqID'])] );
				}else{
					$answerLong="";
					$answer=$txt->sanitize($_POST['Question'.$row['msqID']]);
				}
				
				if( is_array($answer) ) 
					$answer=join(',',$answer);
									
				$questionAnswerPairs[$row['msqID']]['question']=$row['question'];
				$questionAnswerPairs[$row['msqID']]['answer']=$txt->sanitize( $answer.$answerLong );
				
				$v=array($row['msqID'],$answerSetID,$answer,$answerLong);
				$q="insert into {$this->btAnswersTablename} (msqID,asID,answer,answerLong) values (?,?,?,?)";
				$db->query($q,$v);
			}
			$refer_uri=$_POST['pURI'];
			if(!strstr($refer_uri,'?')) $refer_uri.='?';			
			
			if(intval($this->notifyMeOnSubmission)>0){	
							
				$mh = Loader::helper('mail');
				$mh->to( $this->recipientEmail ); 
				$mh->from( $this->formFormEmailAddress ); 
				$mh->addParameter('formName', $this->surveyName);
				$mh->addParameter('questionSetId', $this->questionSetId);
				$mh->addParameter('questionAnswerPairs', $questionAnswerPairs); 
				$mh->load('block_form_submission');
				$mh->setSubject(t('%s Form Submission', $this->surveyName));
				//echo $mh->body.'<br>';
				@$mh->sendMail(); 
			} 
			//$_REQUEST=array();	
			if(!$this->noSubmitFormRedirect){
				header("Location: ".$refer_uri."&surveySuccess=1&qsid=".$this->questionSetId);
				die;
			}
		}
		if ($controller && method_exists($controller,'on_after_submit')) {
			$controller->on_after_submit();
		}
	}		
	
	function delete() { 
	
		$db = Loader::db();

		$deleteData['questionsIDs']=array();
		$deleteData['strandedAnswerSetIDs']=array();

		$miniSurvey=new CF_MiniSurvey();
		$info=$miniSurvey->getMiniSurveyBlockInfo($this->bID);
		
		//get all answer sets
		$q = "SELECT asID FROM {$this->btAnswerSetTablename} WHERE questionSetId = ".intval($info['questionSetId']);
		$answerSetsRS = $db->query($q); 
 
		//delete the questions
		$deleteData['questionsIDs']=$db->getAll( "SELECT qID FROM {$this->btQuestionsTablename} WHERE questionSetId = ".intval($info['questionSetId']).' AND bID='.intval($this->bID) );
		foreach($deleteData['questionsIDs'] as $questionData)
			$db->query("DELETE FROM {$this->btQuestionsTablename} WHERE qID=".intval($questionData['qID']));			
		
		//delete left over answers
		$strandedAnswerIDs = $db->getAll('SELECT fa.aID FROM `btCommunityFormAnswers` AS fa LEFT JOIN btCommunityFormQuestions as fq ON fq.msqID=fa.msqID WHERE fq.msqID IS NULL');
		foreach($strandedAnswerIDs as $strandedAnswerIDs)
			$db->query('DELETE FROM `btCommunityFormAnswers` WHERE aID='.intval($strandedAnswer['aID']));
			
		//delete the left over answer sets
		$deleteData['strandedAnswerSetIDs'] = $db->getAll('SELECT aset.asID FROM btCommunityFormAnswerSet AS aset LEFT JOIN btCommunityFormAnswers AS fa ON aset.asID=fa.asID WHERE fa.asID IS NULL');
		foreach($deleteData['strandedAnswerSetIDs'] as $strandedAnswerSetIDs)
			$db->query('DELETE FROM btCommunityFormAnswerSet WHERE asID='.intval($strandedAnswerSetIDs['asID']));		
		
		//delete the form block		
		$q = "delete from {$this->btTable} where bID = '{$this->bID}'";
		$r = $db->query($q);		
		
		parent::delete();
		
		return $deleteData;
	}
}

/**
 * Namespace for statistics-related functions used by the form block.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class CF_FormBlockStatistics {

	public static function getTotalSubmissions($date = null) {
		$db = Loader::db();
		if ($date != null) {
			return $db->GetOne("select count(asID) from btCommunityFormAnswerSet where DATE_FORMAT(created, '%Y-%m-%d') = ?", array($date));
		} else {
			return $db->GetOne("select count(asID) from btCommunityFormAnswerSet");
		}

	}
	
	public static function loadSurveys($MiniSurvey){  
		$db = Loader::db();
		return $db->query('SELECT s.* FROM '.$MiniSurvey->btTable.' AS s, Blocks AS b, BlockTypes AS bt '.
						  'WHERE s.bID=b.bID AND b.btID=bt.btID AND bt.btHandle="community_form" ' );
	}
	
	public static $sortChoices=array('newest'=>'created DESC','chrono'=>'created');
	
	public static function buildAnswerSetsArray( $questionSet, $orderBy='', $limit='' ){
		$db = Loader::db();
		
		if( strlen(trim($limit))>0 && !strstr(strtolower($limit),'limit')  )
			$limit=' LIMIT '.$limit;
			
		if( strlen(trim($orderBy))>0 && array_key_exists($orderBy, self::$sortChoices) ){
			 $orderBySQL=self::$sortChoices[$orderBy];
		}else $orderBySQL=self::$sortChoices['newest'];
		
		//get answers sets
		$sql='SELECT * FROM btCommunityFormAnswerSet AS aSet '.
			 'WHERE aSet.questionSetId='.$questionSet.' ORDER BY '.$orderBySQL.' '.$limit;
		$answerSetsRS=$db->query($sql);
		//load answers into a nicer multi-dimensional array
		$answerSets=array();
		$answerSetIds=array(0);
		while( $answer = $answerSetsRS->fetchRow() ){
			//answer set id - question id
			$answerSets[$answer['asID']]=$answer;
			$answerSetIds[]=$answer['asID'];
		}		
		
		//get answers
		$sql='SELECT * FROM btCommunityFormAnswers AS a WHERE a.asID IN ('.join(',',$answerSetIds).')';
		$answersRS=$db->query($sql);
		
		//load answers into a nicer multi-dimensional array 
		while( $answer = $answersRS->fetchRow() ){
			//answer set id - question id
			$answerSets[$answer['asID']]['answers'][$answer['msqID']]=$answer;
		}
		return $answerSets;
	}
}

/**
 * Namespace for other functions used by the form block.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class CF_MiniSurvey{

		public $btTable = 'btCommunityForm';
		public $btQuestionsTablename = 'btCommunityFormQuestions';
		public $btAnswerSetTablename = 'btCommunityFormAnswerSet';
		public $btAnswersTablename = 'btCommunityFormAnswers'; 	
		
		public $lastSavedMsqID=0;
		public $lastSavedqID=0;

		function __construct(){
			$db = Loader::db();
			$this->db=$db;
		}

		function addEditQuestion($values,$withOutput=1){
			$jsonVals=array();
			$values['options']=str_replace(array("\r","\n"),'%%',$values['options']); 
			if(strtolower($values['inputType'])=='undefined')  $values['inputType']='field';
			
			//set question set id, or create a new one if none exists
			if(intval($values['qsID'])==0) $values['qsID']=time(); 
			
			//validation
			if( strlen($values['question'])==0 || strlen($values['inputType'])==0  || $values['inputType']=='null' ){
				//complete required fields
				$jsonVals['success']=0;
				$jsonVals['noRequired']=1;
			}else{
				
				if( intval($values['msqID']) ){
					$jsonVals['mode']='"Edit"';
					
					//questions that are edited are given a placeholder row in btCommunityFormQuestions with bID=0, until a bID is assign on block update
					$pendingEditExists = $this->db->getOne( "select count(*) as total from btCommunityFormQuestions where bID=0 AND msqID=".intval($values['msqID']) );
					
					//hideQID tells the interface to hide the old version of the question in the meantime
					$vals=array( intval($values['msqID'])); 		
					$jsonVals['hideQID']=intval($this->db->GetOne("SELECT MAX(qID) FROM btCommunityFormQuestions WHERE bID!=0 AND msqID=?",$vals));	
				}else{
					$jsonVals['mode']='"Add"';
				}
			
				if( $pendingEditExists ){ 
					$width = $height = 0;
					if ($values['inputType'] == 'text'){
						$width  = $this->limitRange(intval($values['width']), 20, 500);
						$height = $this->limitRange(intval($values['height']), 1, 100); 
					}
					$dataValues=array(intval($values['qsID']), trim($values['question']), $values['inputType'],
								      $values['options'], intval($values['position']), $width, $height, intval($values['required']), intval($values['msqID']) );			
					$sql='UPDATE btCommunityFormQuestions SET questionSetId=?, question=?, inputType=?, options=?, position=?, width=?, height=?, required=? WHERE msqID=? AND bID=0';					
				}else{ 
					if( !isset($values['position']) ) $values['position']=1000;
					if(!intval($values['msqID']))
						$values['msqID']=intval($this->db->GetOne("SELECT MAX(msqID) FROM btCommunityFormQuestions")+1); 
					$dataValues=array($values['msqID'],intval($values['qsID']), trim($values['question']), $values['inputType'],
								     $values['options'], intval($values['position']), intval($values['width']), intval($values['height']), intval($values['required']) );			
					$sql='INSERT INTO btCommunityFormQuestions (msqID,questionSetId,question,inputType,options,position,width,height,required) VALUES (?,?,?,?,?,?,?,?,?)'; 
				}
				$result=$this->db->query($sql,$dataValues);  
				$this->lastSavedMsqID=intval($values['msqID']);	
				$this->lastSavedqID=intval($this->db->GetOne("SELECT MAX(qID) FROM btCommunityFormQuestions WHERE bID=0 AND msqID=?", array($values['msqID']) ));
				$jsonVals['qID']=$this->lastSavedqID;
				$jsonVals['success']=1;
			}
			
			$jsonVals['qsID']=$values['qsID'];
			$jsonVals['msqID']=intval($values['msqID']);
			//create json response object
			$jsonPairs=array();
			foreach($jsonVals as $key=>$val) $jsonPairs[]=$key.':'.$val;
			if($withOutput) echo '{'.join(',',$jsonPairs).'}';
		}
		
		function getQuestionInfo($qsID,$qID){
			$questionRS=$this->db->query('SELECT * FROM btCommunityFormQuestions WHERE questionSetId='.intval($qsID).' AND qID='.intval($qID).' LIMIT 1' );
			$questionRow=$questionRS->fetchRow();
			$jsonPairs=array();
			foreach($questionRow as $key=>$val){
				if($key=='options') $key='optionVals';
				$jsonPairs[]=$key.':"'.str_replace(array("\r","\n"),'%%',addslashes($val)).'"';
			}
			echo '{'.join(',',$jsonPairs).'}';
		}

		function deleteQuestion($qsID,$msqID){
			$sql='DELETE FROM btCommunityFormQuestions WHERE questionSetId='.intval($qsID).' AND msqID='.intval($msqID).' AND bID=0';
			$this->db->query($sql,$dataValues);
		} 
		
		function loadQuestions($qsID, $bID=0, $showPending=0 ){
			$db = Loader::db();
			if( intval($bID) ){
				$bIDClause=' AND ( bID='.intval($bID).' ';			
				if( $showPending ) 
					 $bIDClause.=' OR bID=0) ';	
				else $bIDClause.=' ) ';	
			}
			return $db->query('SELECT * FROM btCommunityFormQuestions WHERE questionSetId='.intval($qsID).' '.$bIDClause.' ORDER BY position, msqID');
		}
		
		static function getAnswerCount($qsID){
			$db = Loader::db();
			return $db->getOne( 'SELECT count(*) FROM btCommunityFormAnswerSet WHERE questionSetId='.intval($qsID) );
		}		
		
		function getQuestions($qsID, $bID=0 ){
			$questionsRS=$this->loadQuestions( $qsID, $bID, false);
			$questions = array();
			while( $questionRow=$questionsRS->fetchRow() ){	
				$questions[$questionRow['msqID']] = $questionRow;
			}
			$surveyBlockInfo = $this->getMiniSurveyBlockInfoByQuestionId($qsID,intval($bID));
				
			if($surveyBlockInfo['displayCaptcha']) {
				$captcha = array();
				$captcha['inputType'] = 'captcha';
				$captcha['question'] = t('Please type the letters and numbers shown in the image.');
				$questions['captcha'] = $captcha;
			}			

			return $questions;
		}
		
		function loadSurvey( $qsID, $showEdit=false, $bID=0, $hideQIDs=array(), $showPending=0 ){
		
			//loading questions	
			$questionsRS=$this->loadQuestions( $qsID, $bID, $showPending);
				
			if(!$showEdit){
				echo '<table class="formBlockSurveyTable">';					
				while( $questionRow=$questionsRS->fetchRow() ){					
					if( in_array($questionRow['qID'], $hideQIDs) ) continue;				
						$requiredSymbol=($questionRow['required'])?'&nbsp;<span class="required">*</span>':'';
						echo '<tr>
						        <td valign="top" class="question">'.$questionRow['question'].''.$requiredSymbol.'</td>
						        <td valign="top">'.$this->loadInputType($questionRow,showEdit).'</td>
						      </tr>';
				}			
				$surveyBlockInfo = $this->getMiniSurveyBlockInfoByQuestionId($qsID,intval($bID));
				
				if($surveyBlockInfo['displayCaptcha']) {
					echo '<tr><td colspan="2">';
					echo(t('Please type the letters and numbers shown in the image.'));	
					echo '</td></tr><tr><td>&nbsp;</td><td>';
					
					$captcha = Loader::helper('validation/captcha');				
					$captcha->display();
					print '<br/>';
					$captcha->showInput();	   
					echo '</td></tr>';					
				}			
				echo '<tr><td>&nbsp;</td><td><input class="formBlockSubmitButton" name="Submit" type="submit" value="'.t('Submit').'" /></td></tr>';
				echo '</table>';				
			}
			else{
			
			
				echo '<div id="miniSurveyTableWrap"><div id="miniSurveyPreviewTable" class="miniSurveyTable">';					
				while( $questionRow=$questionsRS->fetchRow() ){	 
				
					if( in_array($questionRow['qID'], $hideQIDs) ) continue;
				
					$requiredSymbol=($questionRow['required'])?'<span class="required">*</span>':'';				
					?>
					<div id="miniSurveyQuestionRow<?php  echo $questionRow['msqID']?>" class="miniSurveyQuestionRow">
						<div class="miniSurveyQuestion"><?php  echo $questionRow['question'].' '.$requiredSymbol.' ID:'.$questionRow['msqID'] ?></div>
						<?php   /* <div class="miniSurveyResponse"><?php  echo $this->loadInputType($questionRow,$showEdit)?></div> */ ?>
						<div class="miniSurveyOptions">
							<div style="float:right">
								<a href="#" onclick="miniSurvey.moveUp(this,<?php  echo $questionRow['msqID']?>);return false" class="moveUpLink"></a> 
								<a href="#" onclick="miniSurvey.moveDown(this,<?php  echo $questionRow['msqID']?>);return false" class="moveDownLink"></a>						  
							</div>						
							<a href="#" onclick="miniSurvey.reloadQuestion(<?php echo intval($questionRow['qID']) ?>);return false"><?php  echo t('edit')?></a> &nbsp;&nbsp; 
							<a href="#" onclick="miniSurvey.deleteQuestion(this,<?php echo intval($questionRow['msqID']) ?>,<?php echo intval($questionRow['qID'])?>);return false"><?php echo  t('remove')?></a>
						</div>
						<div class="miniSurveySpacer"></div>
					</div>
				<?php   }			 
				echo '</div></div>';
			}
		}
		
		function loadInputType($questionData,$showEdit=true){
			$options=explode('%%',$questionData['options']);
			$msqID=intval($questionData['msqID']);
			switch($questionData['inputType']){			
				case 'checkboxlist': 
					$html.= '<div class="checkboxList">'."\r\n";
					for ($i = 0; $i < count($options); $i++) {
						if(strlen(trim($options[$i]))==0) continue;
						$checked=($_REQUEST['Question'.$msqID.'_'.$i]==trim($options[$i]))?'checked':'';
						$html.= '  <div class="checkboxPair"><input name="Question'.$msqID.'_'.$i.'" type="checkbox" value="'.trim($options[$i]).'" '.$checked.' />&nbsp;'.$options[$i].'</div>'."\r\n";
					}
					$html.= '</div>';
					return $html;

				case 'select':
					if($this->frontEndMode){
						$selected=(!$_REQUEST['Question'.$msqID])?'selected':'';
						$html.= '<option value="" '.$selected.'>----</option>';					
					}
					foreach($options as $option){
						$checked=($_REQUEST['Question'.$msqID]==trim($option))?'selected':'';
						$html.= '<option '.$checked.'>'.trim($option).'</option>';
					}
					return '<select name="Question'.$msqID.'" >'.$html.'</select>';
								
				case 'radios':
					foreach($options as $option){
						if(strlen(trim($option))==0) continue;
						$checked=($_REQUEST['Question'.$msqID]==trim($option))?'checked':'';
						$html.= '<div class="radioPair"><input name="Question'.$msqID.'" type="radio" value="'.trim($option).'" '.$checked.' />&nbsp;'.$option.'</div>';
					}
					return $html;
					
				case 'fileupload': 
					$html='<input type="file" name="Question'.$msqID.'" id="" />'; 				
					return $html;
					
				case 'text':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<textarea name="Question'.$msqID.'" cols="'.$questionData['width'].'" rows="'.$questionData['height'].'" style="width:95%">'.$val.'</textarea>';
				case 'header':
					return '<h3>'.$questionRow['question'].'</h3>';
				case 'captcha':
					$captcha = Loader::helper('validation/captcha');				
					ob_start();
					$captcha->display();
					print '<br/>';
					$captcha->showInput();
					$ret = ob_get_contents();
					ob_end_clean();
					return $ret;
				break;
				case 'field':
				default:
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<input name="Question'.$msqID.'" type="text" value="'.stripslashes(htmlspecialchars($val)).'" />';
			}
		}
		
		function getLabel($questionRow) {
			return $questionRow['question'];
		}
		
		function getRequired($questionRow) {
			return ($questionRow['required'])?'&nbsp;<span class="required">*</span>':'';
		}
		
		function getMiniSurveyBlockInfo($bID){
			$rs=$this->db->query('SELECT * FROM btCommunityForm WHERE bID='.intval($bID).' LIMIT 1' );
			return $rs->fetchRow();
		}
		
		function getMiniSurveyBlockInfoByQuestionId($qsID,$bID=0){
			$sql='SELECT * FROM btCommunityForm WHERE questionSetId='.intval($qsID);
			if(intval($bID)>0) $sql.=' AND bID='.$bID;
			$sql.=' LIMIT 1'; 
			$rs=$this->db->query( $sql );
			return $rs->fetchRow();
		}		
		
		function reorderQuestions($qsID=0,$qIDs){
			$qIDs=explode(',',$qIDs);
			if(!is_array($qIDs)) $qIDs=array($qIDs);
			$positionNum=0;
			foreach($qIDs as $qID){
				$vals=array( $positionNum,intval($qID), intval($qsID) );
				$sql='UPDATE btCommunityFormQuestions SET position=? WHERE msqID=? AND questionSetId=?';
				$rs=$this->db->query($sql,$vals);
				$positionNum++;
			}
		}		

		function limitRange($val, $min, $max){
			$val = ($val < $min) ? $min : $val;
			$val = ($val > $max) ? $max : $val;
			return $val;
		}
				
		//Run on Form block edit
		static function questionCleanup( $qsID=0, $bID=0 ){
			$db = Loader::db();
		
			//First make sure that the bID column has been set for this questionSetId (for backwards compatibility)
			$vals=array( intval($qsID) ); 
			$questionsWithBIDs=$db->getOne('SELECT count(*) FROM btCommunityFormQuestions WHERE bID!=0 AND questionSetId=? ',$vals);
			
			//form block was just upgraded, so set the bID column
			if(!$questionsWithBIDs){ 
				$vals=array( intval($bID), intval($qsID) );  
				$rs=$db->query('UPDATE btCommunityFormQuestions SET bID=? WHERE bID=0 AND questionSetId=?',$vals);
				return; 
			} 			
			
			//Then remove all temp/placeholder questions for this questionSetId that haven't been assigned to a block
			$vals=array( intval($qsID) );  
			$rs=$db->query('DELETE FROM btCommunityFormQuestions WHERE bID=0 AND questionSetId=?',$vals);			
		}
}	
?>