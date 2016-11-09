<div id="sidebar">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Top') ) : else : ?>

<?php endif; ?>

<div class="left">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Left') ) : else : ?>

<?php endif; ?>
</div>

<div class="right">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Right') ) : else : ?>

<?php endif; ?>
</div>

<br class="clear" />
</div>