<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package PalmCode_Subs
 */

?>

	<footer id="colophon" class="site-footer bg-form">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<div class="footer-logo mb-4">
						<?php the_custom_logo(); ?>
					</div>
					<div class="footer-company">
						Alarm­anlagen­bau-Korsing<br>
						GmbH & Co. KG
					</div>
					<div class="footer-company-address">
					Walter-Korsing-Straße 21<br>15230 Frankfurt (Oder)
					</div>
				</div>
				<div class="col-md-8">
					<div class="row footer-menu">
						<div class="col-md-4 footer-menu-1">
							<h2>Unternehmen</h2>
							<ul>
								<li>Unser Unternehmen</li>
								<li>Produkte & Hersteller</li>
								<li>Referenzen</li>
								<li>Kontakt</li>
							</ul>
						</div>
						<div class="col-md-4 footer-menu-2">
						<h2>Leistungen</h2>
							<ul>
								<li>Einbruchmeldeanlagen</li>
								<li>Videoüberwachung</li>
								<li>Brandmeldeanlagen</li>
								<li>Rauchabzugsanlagen</li>
								<li>lorem ipsum</li>
							</ul>
						</div>
						<div class="col-md-4 footer-menu-3">
						<h2>Den Kontakt Halten</h2>
							<ul>
								<li>+49 335 545620</li>
								<li>info@alarm­anlagen­bau-korsing.de</li>
							</ul>
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="">
				<div class="mt-5 d-flex justify-content-center align-items-center footer-copyright">
					Impresum &#8901; Datenschutz
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
