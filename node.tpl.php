<?php
// default? need to delete if use default template
?>

<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

<div class="width_full">
<div class="plus1_block">
<?php print render($content['plus1_widget'])?>
</div> 
<div class="text_info_block <?php print('node-'.$node->type);?>"> 
	<?php print render($title_prefix); ?>
  <?php if($page):?>
    <h2 class="title"<?php print $title_attributes; ?>>
    <?php if(isset($news_prefix)){ print '<span class="prefix">'.$news_prefix.'</span>: ';} print $title;?></h2>
  <?php else:?>
     <?php if(isset($news_prefix)) print '<span class="prefix">'.$news_prefix.'</span>: ';?>
     <?php if(isset($website)):?>
      <a class="title" href="<?php print $website ?>"><?php print $title?> </a>
      <?php else:?> 
     <a class="title" href="<?php print $node_url; ?>"><?php print $title; ?></a>
     <?php endif;?>
  <?php endif?>

  <?php if ($display_submitted): ?>
   
      <?php if(isset($paper_authors)){
      	$author=explode(',',$paper_authors);
      	if(count($author)==1||$page){
      		print('<p class="small_text11">'.$paper_authors.' ('.$submitted.')</p>' );
      	}else{
      		print('<p class="small_text11">'.$author[0].' <em>et al </em> <span class="small_text112">('.$submitted.')</span></p>' );
      	}
      }?>
       <?php if(isset($datebegin)) print('<p class="small_text112">'.$datebegin.' - '.$dateend.'</p>'); ?>
      <?php if(isset($city))print('<p>'.$city.'</p>'); ?>
      <?php if(isset($reviewer)) print('<p> reviewed by '.$reviewer.'</p>'); ?>
      <?php if(!isset($paper_authors)) print ('<p class="small_text112">'.$submitted.'</p>'); ?>
      <?php if(isset($download))print('<div class="mt10"><p>'.$download.'</p></div>'); ?>
  
  <?php endif; ?>
    <?php print render($title_suffix); ?>
  
</div>
   </div>
  <div class="content"<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
     if(isset($content['plus1_widget'])) hide($content['plus1_widget']);
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
  </div>



  <?php print render($content['comments']); ?>

</div>