<style type="text/css">
   div#ccm-dashboard-content { margin: 45px 4px 0px 200px ! important}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		function resizeFrame()
		{
         $("#sqlBuddyFrame").css("width", parseInt($("#ccm-dashboard-page").width())-200 + "px")
         $("#sqlBuddyFrame").css("height", parseInt($(document).height())-46 + "px")
		}
		$(window).resize(resizeFrame);
		resizeFrame();
	})
</script>
<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$sqlBuddy = DIR_REL . '/packages/remo_sqlbuddy/libraries/sqlbuddy/index.php';

$_SESSION['SB_LOGIN'] = true;
$_SESSION['SB_LOGIN_STRING'] = "mysql:host=" . DB_SERVER;
$_SESSION['SB_LOGIN_USER'] = DB_USERNAME;
$_SESSION['SB_LOGIN_PASS'] = DB_PASSWORD;
   
?>
<iframe id="sqlBuddyFrame" frameborder="no" border="0" src="<?php  echo $sqlBuddy ?>">
      
</iframe>
