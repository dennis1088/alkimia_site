<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));

class CommunityFormPackage extends Package {

	protected $pkgHandle = 'community_form';
	protected $appVersionRequired = '5.3.3.1';
	protected $pkgVersion = '1.0.1';
	
	public function getPackageDescription() {
		return t("Community maintained form block.");
	}
	
	public function getPackageName() {
		return t("Community form block");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// install block		
		BlockType::installBlockTypeFromPackage('community_form', $pkg);
		
		Loader::model('single_page');
		// add dashboard page
		$dau = SinglePage::add('/dashboard/reports/community_forms', $pkg);
		$dau->update(array('cName'=>t('Community Forms'), 'cDescription'=>t('View results of your community form block')));
		
	}

}