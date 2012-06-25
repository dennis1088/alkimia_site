<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

class SwpBreadcrumbsBlockController extends BlockController {

	var $pobj;

	protected $btTable = 'btSwpBreadcrumbs';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "80";

	public $delimiter = "";

	public function getBlockTypeDescription() {
		return t("Add breadcrumbs to your website<br />By <a href=\"http://www.smartwebprojects.net/\">www.smartwebprojects.net</a>");
	}

	public function getBlockTypeName() {
		return t("Breadcrumbs");
	}

	public function __construct($obj = null) {
		parent::__construct($obj);
	}

	public function view(){
		$this->set('delimiter', $this->delimiter);
		$this->set("homePageLink", $this->getHomePageLink());
	}

	public function save($data) {
		$args['delimiter'] = isset($data['delimiter']) ? $data['delimiter'] : '';
		parent::save($args);
	}
	
	public function getSubLevels() {
		// current page
		$c = Page::getCurrentPage();
	
		$path = array();
		
		$nh = Loader::helper("navigation");
		
		$trail = $nh->getTrailToCollection($c);
		
		array_unshift($path, array(
			"link" => false,
			"title" => $c->vObj->cvName,
		));
		
		if (!empty($trail)) {
			array_pop($trail); // shifting the home page off
			if (!empty($trail)) {
				foreach($trail as $page) {
					$pageLink = false;
					if ($page->getCollectionAttributeValue('replace_link_with_first_in_nav')) {
						$firstChild = $page->getFirstChild();
						if ($firstChild instanceof Page) {
							$pageLink = $nh->getCollectionURL($firstChild);
						}
					}
					if (empty($pageLink)) {
						$pageLink = $nh->getCollectionURL($page);
					}
					array_unshift($path, array(
							"link" => $pageLink,
							"title" => $page->vObj->cvName,
						));
				}	
			}			
		}
		
		return $path;
	}
	
	function getHomePageLink() {
		$homepage = Page::getByID(HOME_CID);
		$link = DIR_REL . "/";
		if ($homepage->getCollectionAttributeValue('replace_link_with_first_in_nav')) {
			$nh = Loader::helper("navigation");
			$firstChild = $homepage->getFirstChild();
			if ($firstChild instanceof Page) {
				$link = $nh->getCollectionURL($firstChild);
			}
		}
		return $link;
	}
	
}
