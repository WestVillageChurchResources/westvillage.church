<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?> class="no-js">

<head>
<?php
// Fire the wp_head action required for hooking in scripts, styles, and other <head> tags.
wp_head();
?>
</head>

<body <?php hoot_attr( 'body' ); ?>>

	<div id="page-wrapper" <?php hoot_attr( 'page-wrapper' ); ?>>

		<div class="skip-link">
			<a href="#content" class="screen-reader-text"><?php _e( 'Skip to content', 'dispatch' ); ?></a>
		</div><!-- .skip-link -->

		<?php
		// Template modification Hook
		do_action( 'hoot_template_site_start' );

		// Displays a friendly note to visitors using outdated browser (Internet Explorer 8 or less)
		hoot_update_browser();
		?>

		<?php get_template_part( 'template-parts/topbar' ); ?>

		<header <?php hoot_attr( 'header', '', 'contrast-typo' ); ?>>
			<div class="grid">
				<div class="table grid-span-12">

					<div id="branding" class="table-cell-mid">
						<div id="site-logo">
							<?php
							// Display the Image Logo or Site Title
							hoot_logo();
							?>
						</div>
					</div><!-- #branding -->

					<div id="header-aside" class="table-cell-mid">
						<?php
						// Loads the template-parts/menu-primary.php template.
						hoot_get_menu( 'primary' );
						?>
					</div>

				</div>
			</div>
		</header><!-- #header -->

		<div id="main" class="main">
			<?php
			// Template modification Hook
			do_action( 'hoot_template_main_wrapper_start' );