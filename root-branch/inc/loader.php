<?php
/**
 * First-visit loader (the seal grows like a tree).
 *
 * The head script arms it only on the first page view of a browser session
 * and never for prefers-reduced-motion; the markup is inert otherwise.
 * The caption is the site tagline (Settings → General → Tagline).
 *
 * @package RootBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Decide before first paint whether this visit gets the loader.
 */
add_action(
	'wp_head',
	static function () {
		?>
<script>(function(d){try{if(matchMedia('(prefers-reduced-motion: reduce)').matches||sessionStorage.getItem('rbSeen'))return;sessionStorage.setItem('rbSeen','1');d.classList.add('rb-loading');}catch(e){}})(document.documentElement);</script>
<style>
.rb-loader{display:none}
.rb-loading .rb-loader{align-items:center;background:#f5f0e9;display:flex;flex-direction:column;gap:clamp(1.25rem,3vh,2rem);inset:0;justify-content:center;position:fixed;transition:opacity .45s cubic-bezier(.65,0,.35,1),visibility .45s;z-index:100000}
.rb-loader.is-done{opacity:0;visibility:hidden}
.rb-loader__seal{aspect-ratio:1;position:relative;width:min(44vmin,280px)}
.rb-loader__seal>div{inset:0;position:absolute}
.rb-loader__seal svg{display:block;height:100%;overflow:visible;width:100%}
.rb-loader__ghost{filter:grayscale(1);opacity:0}
.rb-loader__colour{-webkit-mask-image:linear-gradient(to top,#000 0%,#000 44%,transparent 56%);mask-image:linear-gradient(to top,#000 0%,#000 44%,transparent 56%);-webkit-mask-position:0 0%;mask-position:0 0%;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-size:100% 260%;mask-size:100% 260%;opacity:0}
.rb-loader__caption{color:#2c1a00;font-family:Poppins,"Helvetica Neue",Arial,sans-serif;font-size:clamp(.6875rem,.62rem + .3vw,.875rem);font-weight:300;letter-spacing:.28em;margin:0;opacity:0;padding-inline:1.5rem;text-align:center;text-transform:uppercase}
.rb-loading body{overflow:hidden}
.rb-loading .is-style-rb-hero{clip-path:circle(1.5% at 50% 50%)}
</style>
		<?php
	},
	3
);

add_action(
	'wp_body_open',
	static function () {
		$caption = get_bloginfo( 'description' );
		printf(
			'<div id="rb-loader" class="rb-loader" aria-hidden="true" data-src="%1$s"><div class="rb-loader__seal"></div>%2$s</div>',
			esc_url( get_theme_file_uri( 'assets/images/logo-draw.svg' ) ),
			$caption ? '<p class="rb-loader__caption">' . esc_html( $caption ) . '</p>' : ''
		);
		$script = get_theme_file_path( 'assets/js/loader.js' );
		if ( file_exists( $script ) ) {
			echo '<script>' . file_get_contents( $script ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- theme-owned static script.
		}
	},
	1
);
