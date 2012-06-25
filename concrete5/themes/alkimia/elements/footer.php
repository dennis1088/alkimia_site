<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="clear"></div>
	
	<div id="main-footer" class="container">
		<div id="footer-image" class="row">
			<div class="row">
			<div id="footer-content-address" class="threecol">
				<?php  
					$a = new Area('SocialLink');
					$a->display($c);
				?>
			</div>
			<div id="footer-content-address" class="threecol">
				<?php  
					$a = new Area('FooterAddress');
					$a->display($c);
				?>
			</div>
			<div id="footer-content-contact" class="threecol">
				<?php  
					$a = new Area('FooterContact');
					$a->display($c);
				?>
			</div>
			<div id="footer-content-site-map" class="threecol last">
				<?php  
					$a = new Area('FooterSiteMap');
					$a->display($c);
				?>
			</div>
			</div>
			
		</div>
	</div>
	<div id="copyright-container" class="container">
		<div class="row">
			<div id="login-block" class="twelvecol last">
				<p class="footer-sign-in">
				<?php  
					$u = new User();
					if ($u->isRegistered()) { ?>
					<?php   
						if (Config::get("ENABLE_USER_PROFILES")) {
							$userName = '<a href="' . $this->url('/profile') . '">' . $u->getUserName() . '</a>';
						} else {
							$userName = $u->getUserName();
						}
					?>
					<span class="sign-in">
						<?php  echo t('Currently logged in as <b>%s</b>.', $userName)?> 
						<a href="<?php  echo $this->url('/login', 'logout')?>"><?php  echo t('Sign Out')?></a>
					</span>
					<?php   } else { ?>
					<span class="sign-in">
						<a href="<?php  echo $this->url('/login')?>"><?php  echo t('Sign In to Edit this Site')?></a>
					</span>
				<?php   } ?>
				<span class="sign-in">&copy;<?php  echo date('Y')?> <?php  echo SITE?>.</span>
				</p>	
			</div>
		</div>
	</div>


<?php   Loader::element('footer_required'); ?>

</body>
</html>