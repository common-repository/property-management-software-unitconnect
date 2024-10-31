<?php
/**
 * Single listing contacts
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/contacts.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$show_contacts = ucpm_meta( 'show_contacts' );
$contact_1 = ucpm_meta( 'contact_1' );
$contact_2 = ucpm_meta( 'contact_2' );
$contact_3 = ucpm_meta( 'contact_3' );
$contact_4 = ucpm_meta( 'contact_4' );

if ( isset( $show_contacts ) && $show_contacts === 'on' ) : ?>
    <div class="contacts sidebar-item">
        <h6><?php esc_html_e('Contact(s):', 'ucpm'); ?></h6>

        <?php
        /**
         * Contact 1.
         */

        if ( ! empty( $contact_1 ) ) :
            $user_data = get_userdata( $contact_1 );

            $username = ! empty( $user_data->first_name ) && ! empty( $user_data->last_name ) ? $user_data->first_name . ' ' . $user_data->last_name : $user_data->user_nicename;
            $avatar_url = get_user_meta( $user_data->ID, 'ucpm_user_avatar', true );
            $tel = get_user_meta( $user_data->ID, 'ucpm_user_tel', true );
            $tel_mobile = get_user_meta( $user_data->ID, 'ucpm_user_tel_mobile', true );
            ?>
            <div class="contacts-item">
                <div class="contacts-item-avatar">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="">
                </div>

                <div class="contacts-item-content">
                    <h4 class="contacts-item-el contacts-item-name"><?php echo esc_html( $username ); ?></h4>
                    <a href="mailto:<?php echo sanitize_email( $user_data->user_email ); ?>" class="contacts-item-el contacts-item-email">e: <?php echo sanitize_email( $user_data->user_email ); ?></a>

                    <?php if ( ! empty( $tel ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">o: <?php echo esc_html( $tel ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $tel_mobile ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">m: <?php echo esc_html( $tel_mobile ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Contact 2.
         */

        if ( ! empty( $contact_2 ) ) :
            $user_data = get_userdata( $contact_2 );

            $username = ! empty( $user_data->first_name ) && ! empty( $user_data->last_name ) ? $user_data->first_name . ' ' . $user_data->last_name : $user_data->user_nicename;
            $avatar_url = get_user_meta( $user_data->ID, 'ucpm_user_avatar', true );
            $tel = get_user_meta( $user_data->ID, 'ucpm_user_tel', true );
            $tel_mobile = get_user_meta( $user_data->ID, 'ucpm_user_tel_mobile', true );
            ?>
            <div class="contacts-item">
                <div class="contacts-item-avatar">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="">
                </div>

                <div class="contacts-item-content">
                    <h4 class="contacts-item-el contacts-item-name"><?php echo esc_html( $username ); ?></h4>
                    <a href="mailto:<?php echo sanitize_email( $user_data->user_email ); ?>" class="contacts-item-el contacts-item-email">e: <?php echo sanitize_email( $user_data->user_email ); ?></a>

                    <?php if ( ! empty( $tel ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">o: <?php echo esc_html( $tel ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $tel_mobile ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">m: <?php echo esc_html( $tel_mobile ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Contact 3.
         */
        if ( ! empty( $contact_3 ) ) :
            $user_data = get_userdata( $contact_3 );

            $username = ! empty( $user_data->first_name ) && ! empty( $user_data->last_name ) ? $user_data->first_name . ' ' . $user_data->last_name : $user_data->user_nicename;
            $avatar_url = get_user_meta( $user_data->ID, 'ucpm_user_avatar', true );
            $tel = get_user_meta( $user_data->ID, 'ucpm_user_tel', true );
            $tel_mobile = get_user_meta( $user_data->ID, 'ucpm_user_tel_mobile', true );
            ?>
            <div class="contacts-item">
                <div class="contacts-item-avatar">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="">
                </div>

                <div class="contacts-item-content">
                    <h4 class="contacts-item-el contacts-item-name"><?php echo esc_html( $username ); ?></h4>
                    <a href="mailto:<?php echo sanitize_email( $user_data->user_email ); ?>" class="contacts-item-el contacts-item-email">e: <?php echo sanitize_email( $user_data->user_email ); ?></a>

                    <?php if ( ! empty( $tel ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">o: <?php echo esc_html( $tel ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $tel_mobile ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">m: <?php echo esc_html( $tel_mobile ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Contact 4.
         */

        if ( ! empty( $contact_4 ) ) :
            $user_data = get_userdata( $contact_4 );

            $username = ! empty( $user_data->first_name ) && ! empty( $user_data->last_name ) ? $user_data->first_name . ' ' . $user_data->last_name : $user_data->user_nicename;
            $avatar_url = get_user_meta( $user_data->ID, 'ucpm_user_avatar', true );
            $tel = get_user_meta( $user_data->ID, 'ucpm_user_tel', true );
            $tel_mobile = get_user_meta( $user_data->ID, 'ucpm_user_tel_mobile', true );
            ?>
            <div class="contacts-item">
                <div class="contacts-item-avatar">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="">
                </div>

                <div class="contacts-item-content">
                    <h4 class="contacts-item-el contacts-item-name"><?php echo esc_html( $username ); ?></h4>
                    <a href="mailto:<?php echo sanitize_email( $user_data->user_email ); ?>" class="contacts-item-el contacts-item-email">e: <?php echo sanitize_email( $user_data->user_email ); ?></a>

                    <?php if ( ! empty( $tel ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">o: <?php echo esc_html( $tel ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $tel_mobile ) ) : ?>
                        <span class="contacts-item-el contacts-item-tel">m: <?php echo esc_html( $tel_mobile ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif;
