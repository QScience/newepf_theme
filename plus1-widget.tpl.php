<?php
/**
 * @file
 * Template implementation to display the node widget.
 */
?>
<div class="vote_block">
<div class="vote_no"> <?php print $score; ?></div>
vote
<div class="mt5">
<?php if($can_vote) : ?>
        <div class="<?php if (!$voted) print 'plus1-vote';?>">
          <?php print $widget_message; ?>
        </div>
      <?php else: ?>
        <?php print $widget_message; ?>
      <?php endif; ?>
</div>
</div>