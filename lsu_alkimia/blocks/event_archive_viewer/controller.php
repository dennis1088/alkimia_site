<?php
	class EventArchiveViewerBlockController extends BlockController {
		
		var $pobj;
		protected $btDescription = "Display events stored in the events archive.";
		protected $btName = "Event Archive Viewer";
		protected $btTable = 'bteventarchiveviewer';
		protected $btInterfaceWidth = "350";
		protected $btInterfaceHeight = "300";

		public function getNumEntries() {
			$db= Loader::db();
			$query = "SELECT * FROM " . $this->btTable . " WHERE bID = " . $this->getBlockObject()->getBlockID();
			$result = $db->Execute($query) or die("Error in query: $query. " . $db->ErrorMsg());
			return $result->fields['numentries'];
		}
		
		public function getCSSColType() {
			return array(
    				1 => "onecol", 2 => "twocol",3 => "threecol",4 => "fourcol",
					5 => "fivecol", 6 => "fixcol",7 => "sevencol",8 => "eightcol",
					9 => "ninecol", 10 => "tencol",11 => "elevencol",12 => "twelvecol"
					);
		}
		
		public function getFieldFromNthMostRecentEvent($fieldname, $n) {
			$db= Loader::db();
			$query = "SELECT asID, questionSetId FROM btCommunityFormAnswerSet ORDER BY created DESC LIMIT 1 OFFSET $n";
			$result = $db->Execute($query) or die("Error in query: $query. " . $db->ErrorMsg());
			$asID = $result->fields['asID'];
			$qsID = $result->fields['questionSetId'];
			$query = "SELECT msqID FROM btCommunityFormQuestions WHERE questionSetId=$qsID AND question='$fieldname'";
			$result = $db->Execute($query) or die("Error in query: $query. " . $db->ErrorMsg());
			$msqID = $result->fields['msqID'];
			$query = "SELECT answer FROM btCommunityFormAnswers WHERE asID=$asID AND msqID=$msqID";
			$result = $db->Execute($query) or die("Error in query: $query. " . $db->ErrorMsg());
			$answer = $result->fields['answer'];
			if ($fieldname == "Image") {
				$image_file = empty($answer) ? null : File::getByID($answer);
				$ih = Loader::helper('image');
				return $ih->getThumbnail($image_file, 150, 130)->src;
			} else {
				return $answer;
			}
		}
	}
	
?>