<?php
// $Id: block.tpl.php,v 1.2 2007/08/07 08:39:36 goba Exp $
?>
<div class="<?php print "block block-$block->module" ?>" id="<?php print "block-$block->module-$block->delta"; ?>">

<div class="content <?php if ( $block->region=="sidebar_first"):?> mt15 <?php endif?>">
<?php if ($block->subject&& $block->region!="footer"):?>
<h2 class="heading_text"><?php print $block->subject ?></h2>
<?php endif ?>
<div class="content_box">
 <div><?php print $content ?></div>
</div>

</div>
</div>

