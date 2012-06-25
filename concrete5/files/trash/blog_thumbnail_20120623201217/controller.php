<?php      

defined('C5_EXECUTE') or die(_("Access Denied."));

class BlogThumbnailPackage extends Package {

	protected $pkgHandle = 'blog_thumbnail';
	protected $appVersionRequired = '5.3.0';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Allows you to have blog thumbnails.");
	}
	
	public function getPackageName() {
		return t("Blog Thumbnail");
	}
	public function install() {
		$pkg = parent::install();
	  	Loader::model('collection_types');
		Loader::model('collection_attributes');
		Loader::model('attribute/categories/collection');
		$ift = AttributeType::getByHandle('image_file');
		$blogEntryThumb = CollectionType::getByHandle('blog_entry_thumbnail');
		if(!$blogEntryThumb || !intval($blogEntryThumb->getCollectionTypeID())){
			$blogEntry = CollectionType::add(array('ctHandle'=>'blog_entry_thumbnail','ctName'=>t('Blog Entry Thumbnail')),$pkg);
			//install page type if it doesn't exist
		}
		$blogThumbnail=CollectionAttributeKey::getByHandle('blog_thumbnail');
		if( !is_object($blogThumbnail) ){
     		CollectionAttributeKey::add($ift, array('akHandle' => 'blog_thumbnail', 'akName' => t('Blog Thumbnail'), 'akIsSearchable' => false));
     		$pageType = CollectionType::getByHandle('blog_entry_thumbnail');
			$ak= CollectionAttributeKey::getByHandle('blog_thumbnail');
			$pageType->assignCollectionAttribute($ak);

     		//install attribute and set default settings
     	}

	}	
  function uninstall(){
  	parent::uninstall();
  }
}

