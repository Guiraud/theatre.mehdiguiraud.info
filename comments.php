<?php
/**
 * Commentaires.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="commentaires">
	<?php if ( have_comments() ) : ?>
		<h2 class="commentaires__titre">
			<?php
			printf(
				/* translators: %s: nombre de réponses. */
				esc_html( _n( '%s réponse versée', '%s réponses versées', get_comments_number(), 'refoulement' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 0,
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => esc_html__( 'Réponses précédentes', 'refoulement' ),
			'next_text' => esc_html__( 'Réponses suivantes', 'refoulement' ),
		) );
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="cote"><?php esc_html_e( 'Les réponses sont closes sur cette pièce.', 'refoulement' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'        => esc_html__( 'Verser une réponse', 'refoulement' ),
		'class_submit'       => 'submit bouton bouton--rouge',
		'comment_notes_before' => '<p class="cote">' . esc_html__( 'Votre adresse n’est pas publiée.', 'refoulement' ) . '</p>',
	) );
	?>
</section>
