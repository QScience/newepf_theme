<?php
/**
 * @file panels-pane.tpl.php
 * Main panel pane template
 *
 * Variables available:
 * - $pane->type: the content type inside this pane
 * - $pane->subtype: The subtype, if applicable. If a view it will be the
 *   view name; if a node it will be the nid, etc.
 * - $title: The title of the content
 * - $content: The actual content
 * - $links: Any links associated with the content
 * - $more: An optional 'more' link (destination only)
 * - $admin_links: Administrative links associated with the content
 * - $feeds: Any feed icons or associated with the content
 * - $display: The complete panels display object containing all kinds of
 *   data including the contexts and all of the other panes being displayed.
 */
?>
<?php if ($pane_prefix): ?>
  <?php print $pane_prefix; ?>
<?php endif; ?>
<div class="<?php print $classes; ?>" <?php print $id; ?>>
  <?php if ($admin_links): ?>
    <?php print $admin_links; ?>
  <?php endif; ?>

  <?php print render($title_prefix); ?>
  <?php if ($title): if($pane->panel=='top'||$pane->panel=='bottom'):?>
  	<div class="border_title">
		<h2 class="base4font man2_pic_h2 <?php if($pane->panel=='top'):?>man2_picblock<?php else:?>paper_picblock<?php endif;?>"><?php print $title; ?></h2>
		<a class="plus_minus_link" href="<?php if($pane->panel=='top') print url('node/add/editorial');else print url('add/paper');?>">Add</a>
		<div class="clear"></div>
	</div>
	<?php elseif(substr($pane->panel,0,4)=='left'||substr($pane->panel,0,5)=='right'):?>
	<div class="col_block">
		<h2 ><?php print $title; ?></h2>
		<a class="plus_minus_link" href="<?php
		 if($title=="News")
		 print url('node/add/news');
		 elseif(substr($title,0,7)=="Current")
		 print url('node/add/event');
		 elseif($title=="Book reviews")
		 print url('node/add/bookreview');
		 elseif($title=="Latest Blogs")
		 print url('node/add/blog');		 
		 ?>">add</a>
		<div class="clear"></div>
		</div>
	<?php else:?>
		<h2<?php print $title_attributes; ?>><?php print $title; ?></h2>
  <?php endif;endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($feeds): ?>
    <div class="feed">
      <?php print $feeds; ?>
    </div>
  <?php endif; ?>
  
  <?php if($pane->panel=='top'):?>
  <div id="editorial_block" >
  <?php elseif($pane->panel=='bottom'):?>
  <div id="paper_info_block" >
  <?php elseif(substr($pane->panel,0,4)=='left'||substr($pane->panel,0,5)=='right'):?>
  <div class="textblock" >
  <?php else:?>
  <div class="pane-content" >
  <?php endif;?>
     <?php if(is_array($content) && isset($content['blog_more'])){$blog=$content['blog_more'];hide($content['blog_more']);}
          print render($content);?>
  </div>
  

  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>

<?php if ($pane->panel=='bottom'): ?>
    <div class="paper-more-link">
      <?php print '<a class="red_see_more" href="'.url('papers/popular').'">See the most popular papers</a>'; ?>
    </div>
  <?php elseif ($more): ?>
    <div class="more-link">
      <?php print $more; ?>
    </div>
  <?php elseif (is_array($content) && isset($content['blog_more'])): ?>
      <?php print render($blog); ?>
   <?php elseif (substr($title,0,7)=="Current"): ?>
       <div class="more-link">
      <a href="<?php print url('fevents')?>">Show more</a>
    </div>
  <?php endif; ?>
</div>
<?php if ($pane_suffix): ?>
  <?php print $pane_suffix; ?>
<?php endif; ?>
