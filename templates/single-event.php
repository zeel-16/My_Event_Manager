<?php
/**
 * The template for displaying a single event.
 *
 * This template will be used if the theme does not provide a single-event.php.
 *
 * @package My_Event_Manager
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while ( have_posts() ) :
            the_post();

            // Get custom fields for the current event.
            $event_date              = get_post_meta( get_the_ID(), '_my_event_manager_event_date', true );
            $event_time              = get_post_meta( get_the_ID(), '_my_event_manager_event_time', true );
            $event_location          = get_post_meta( get_the_ID(), '_my_event_manager_event_location', true );
            $event_speakers          = get_post_meta( get_the_ID(), '_my_event_manager_event_speakers', true );
            $event_short_description = get_post_meta( get_the_ID(), '_my_event_manager_event_short_description', true );

            // Format the date for display
            $formatted_date = ! empty( $event_date ) ? date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) : '';
            $formatted_time = ! empty( $event_time ) ? date_i18n( get_option( 'time_format' ), strtotime( '1970-01-01 ' . $event_time ) ) : '';
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><div class="entry-content">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="event-single-thumbnail">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="event-details">
                        <?php if ( ! empty( $formatted_date ) ) : ?>
                            <p><strong><?php _e( 'Date:', 'my-event-manager' ); ?></strong> <?php echo esc_html( $formatted_date ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $formatted_time ) ) : ?>
                            <p><strong><?php _e( 'Time:', 'my-event-manager' ); ?></strong> <?php echo esc_html( $formatted_time ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $event_location ) ) : ?>
                            <p><strong><?php _e( 'Location:', 'my-event-manager' ); ?></strong> <?php echo esc_html( $event_location ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $event_speakers ) ) : ?>
                            <p><strong><?php _e( 'Speaker(s):', 'my-event-manager' ); ?></strong> <?php echo esc_html( $event_speakers ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $event_short_description ) ) : ?>
                            <p><strong><?php _e( 'Short Description:', 'my-event-manager' ); ?></strong> <?php echo esc_html( $event_short_description ); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php the_content(); // Displays the main content (editor) of the event post ?>
                </div><footer class="entry-footer">
                    <?php edit_post_link( __( 'Edit Event', 'my-event-manager' ), '<span class="edit-link">', '</span>' ); ?>
                </footer></article><?php
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>

    </main></div><?php
get_sidebar();
get_footer();