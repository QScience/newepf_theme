
<div class="user-history"><?php print theme('username', array('account'=>$elements['#account'],'picture-size'=>13))?><span style="color:#D61C18;">'s </span>
<?php if(arg(0)=='user'):?>
Profile | 
<?php else:?>
<a href="<?php print url('users/'.$elements['#account']->name);?>" >Profile</a> | 
<?php endif;?>
<?php if(arg(0)=='user-history'):?>
Submissions (<?php print $historyNo['post']?>) | 
<?php else:?>
<a href="<?php print url('user-history/'.$elements['#account']->name);?>" >Submissions (<?php print $historyNo['post']?>)</a> | 
<?php endif;?>
<?php if(arg(0)=='user-comments'):?>
Comments (<?php print $historyNo['comment']?>) | 
<?php else:?>
<a href="<?php print url('user-comments/'.$elements['#account']->name);?>" >Comments (<?php print $historyNo['comment']?>)</a> | 
<?php endif;?>
<?php if(arg(0)=='blog'):?>
Blogs (<?php print $historyNo['blog']?>)
<?php else:?>
<a href="<?php print url('blogs/'.$elements['#account']->name);?>">Blogs (<?php print $historyNo['blog']?>)</a>
<?php endif;?>
</div>
<?php if(!isset($isprofilepage)):?>
<div class="profile"<?php print $attributes; ?>>
  <?php print render($user_profile); ?>
  <div class='clear'></div>
</div>
<?php endif;?>