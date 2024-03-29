<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>

		<div class="row">
			<div id="footer">
				<div class="fourcol" id="left-footer">
					<?php $a = new GlobalArea('LeftFooter'); $a->display($c);?>
				</div>
				<div class="fourcol" id="mid-footer">
					<?php $a = new GlobalArea('MidFooter'); $a->display($c);?>
				</div>
				<div class="fourcol last" id="right-footer">
						<?php $a = new GlobalArea('RightFooter'); $a->display($c);?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
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