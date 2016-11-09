<footer <?php hoot_attr( 'footer', '', 'footer grid-stretch contrast-typo' ); ?>>
	<div class="grid">
		<?php
		$columns = hoot_get_footer_columns();
		$alphas = range('a', 'e');
		$structure = hoot_footer_structure();
		?>

		<?php for ( $i=0; $i < $columns; $i++ ) { ?>
			<div class="<?php echo 'grid-span-' . $structure[ $i ] ; ?> footer-column">
				<?php dynamic_sidebar( 'footer-' . $alphas[ $i ] ); ?>
			</div>
		<?php } ?>
	</div>
</footer><!-- #footer -->