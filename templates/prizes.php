<?php 
/*
Inventive lotteries and giveaways

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA

*/
/*

You can put this too inside prize details
<div class="ivl_prize_image"><?php echo get_the_post_thumbnail($prize->prize_id, 'medium'); ?></div>
*/
?>
<div class="ivl_prize_to_win_title"><?php echo sprintf(__('There are <span class="ivl_no_h1">%1$s</span> prizes to win!','inventive_lottery'), $count_prizes); ?></div>

<div class="ivl_prizes_to_win">
<?php
foreach ($prizes_to_win as $prize):
?>

<div class="ivl_prize_to_win">

<div class="ivl_prize_total"><span class="ivl_no"><?php echo __("no.","inventive_lottery"); ?></span><?php echo $prize->total; ?></div>
<div class="ivl_prize_name"><?php echo $prize->name; ?></div>
<?php if ($prize->description || $prize->value) { ?>
<div class="ivl_prize_details">
<?php if ($prize->value > 0) { ?>
<div class="ivl_prize_value"><?php echo __("Prize value","inventive_lottery")." ".woocommerce_price($prize->value); ?></div>
<?php } ?>

</div>
<?php } ?>

</div>
<?php
endforeach;
?>
</div><!-- end prizes to win container -->