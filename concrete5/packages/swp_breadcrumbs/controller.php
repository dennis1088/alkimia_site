<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

class SwpBreadcrumbsPackage extends Package {

	protected $pkgHandle = 'swp_breadcrumbs';
	protected $appVersionRequired = '5.3.0';
	protected $pkgVersion = '2.0';

	public function getPackageDescription() {
		return t('Allows to add breadcrumbs navigation as a block.<br />By <a href="http://www.smartwebprojects.net/">www.smartwebprojects.net</a>');
	}

	public function getPackageName() {
		return t('Breadcrumbs');
	}

	public function install() {
		$pkg = parent::install();

		Loader::model('block_types');

		// install block
		BlockType::installBlockTypeFromPackage('swp_breadcrumbs', $pkg);

	}

	public function upgrade() {
		$old_version = $this->getPackageVersion();
		
		parent::upgrade();
		
		switch ($old_version) {
			case "1.0":
				$this->upgrade_1_0();
				break;
			default:
				// version not in the list
				break;
		}				
	}
	
	function upgrade_1_0() {
		$db = Loader::db();
		
		/* since the block table has been changed from btContentLocal to btSwpBreadcrumbs,
		we need to move records from one table to another for corresponding block instances */
		
		// finding btID for "swp_breadcrumbs"
		$breadcrumbs_btID = $db->GetOne("select btID from BlockTypes where btHandle = 'swp_breadcrumbs'");
		
		// taking all blocks with block_type == "swp_breadcrumbs" (btID = ?)
		$blockIDs = $db->GetCol("select bID from Blocks where btID=?", array($breadcrumbs_btID));
		
		// for each block
		foreach($blockIDs as $blockID) {
			// getting value from btContentLocal
			$cl = $db->GetOne("select bID from btContentLocal where bID=?", array($blockID));
			if (!empty($cl)) {		
				// creating record in btSwpBreadcrumbs
				$db->Execute("insert into btSwpBreadcrumbs set bID=?, delimiter=?", array(
						"bID" => $blockID,
						"delimiter" => '>',
					));
				// removing record from btContentLocal
				$db->Execute("delete from btContentLocal where bID=?", array($blockID));
			}
		}
	}
	
}