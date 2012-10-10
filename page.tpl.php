<div class="main">

		<a href="#" id="fixed_link">Fixed link</a>
		<div id="header">
			<div class="logo">
				<h1>
					<a href="<?php print $front_page ?>" title="<?php print t('Home') ?>">EconoPhysics</a>
				</h1>
			</div>
			<div id="menu">
				<?php  print theme('links__system_main_menu', array('links' => $main_menu));?>  
			</div>
			<div class="clear"></div>
		</div>
		
		<div id="search_bar">
		<?php print render($page['header']); ?>
			<?php if ($logged_in) : ?>
			<div class="login_user">
     			<?php print t("Hello ") ?> <strong><?php print theme('username', array('account'=>$user,'picture-size'=>15))?></strong> |
        		<a href="<?php print url('user/logout')?>"><?php print t("Log out") ?></a>
        	</div>
     		<?php else: ?>
     			<a href="<?php print url('user/register')?>" class="button_block black_button" style="margin-left:100px"><?php print t("Join Now")?>!</a>
				<a href="<?php print url('user/login')?>" class="button_block red_button"><?php print t("Sign In")?></a>
      		<?php endif ?>
		</div>
		
		<div id="content_block">
			<div id="left_side"><!-- left side -->
			<?php print $messages; ?>
 	
 		<?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
 		
 		<?php if (!isset($node)):?>
        <?php print render($title_prefix); ?>
        <?php if ($title): ?><div class="border_title" id="page-title">
        <h2 class="base4font man2_pic_h2"><?php print $title; ?></h2>
        <?php if(isset($add_links)):?>
        <a class="plus_minus_link" href="<?php print $add_links; ?>">Add</a>
        <?php endif;?>
        </div><?php endif; ?>
        <?php print render($title_suffix); ?>
        <?php endif?>
        
        <?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
        <?php print render($page['help']); ?>
        <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
        <?php print render($page['content']); ?>
        <?php print $feed_icons; ?>
				
			</div><!-- left side end-->
				
			<div id="right_side">
				<?php if ($page['sidebar_first']): ?>
          		<?php print render($page['sidebar_first']); ?>
    			<?php endif; ?>
				
			</div>
		</div>			
		<div class="clear"></div>
		
		<div id="footer">
			<?php if ($page['footer']): ?>
			<?php print render($page['footer']); ?>
			<?php endif; ?>
		</div>
		
	</div>
