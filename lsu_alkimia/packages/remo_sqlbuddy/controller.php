<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class RemoSqlbuddyPackage extends Package {

   protected $pkgHandle = 'remo_sqlbuddy';
   protected $appVersionRequired = '5.3.3';
   protected $pkgVersion = '1.0';

   public function getPackageDescription() {
      return t("Installs the SQL Buddy package.");
   }

   public function getPackageName() {
      return t("SQL Buddy");
   }

   public function install() {
      $pkg = parent::install();

      Loader::model('single_page');
         
      // install pages
      $sp1 = SinglePage::add('/dashboard/remo_sqlbuddy', $pkg);
      $sp1->update(array('cName'=>t('SQL Buddy'), 'cDescription'=>t('Database management.')));
    
   }
}
?>
