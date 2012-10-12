<div class="profile"<?php print $attributes; ?>>
  <?php print render($user_profile); ?>
  <div class='clear'></div>
</div>

<h2 class="sub-title" id="history">History of contributions</h2>
<div class="user-history">
<div style="padding-left: 20px;">
<p><a href="<?php print url('user-history/'.$elements['#account']->name);?>" style="font-weight: bold;">Submissions (<?php print $historyNo['post']?>)</a></p>
<p><a href="<?php print url('user-comments/'.$elements['#account']->name);?>" style="font-weight: bold;">Comments (<?php print $historyNo['comment']?>)</a></p>
<p><a href="<?php print url('blogs/'.$elements['#account']->name);?>" style="font-weight: bold;">Blogs (<?php print $historyNo['blog']?>)</a></p>
</div>
</div>
