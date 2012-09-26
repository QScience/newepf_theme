<?php	
$items = array();
  $number = variable_get('comment_block_count', 10);
  foreach (comment_get_recent($number) as $comment) {
    $items[] = l($comment->subject, 'comment/' . $comment->cid, array('fragment' => 'comment-' . $comment->cid)) . 
		'<div class="small_text_12">by '.l($comment->name,'user/'.$comment->uid,array('attributes' => array ('class'=>array('username')))) . t(' (@time)', array('@time' => format_date($comment->changed))) . '</div>';
  }

  if ($items) {
    print theme('item_list', array('items' => $items,'attributes'=>array ('class' => array('red_arrow_li'))));
  }
 else {
    print t('No comments available.');
  }