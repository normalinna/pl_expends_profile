<?php
/**
 * The template for displaying all profile
 *
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
?>
<section id="primary" class="content-area">
		<main id="main" class="site-main center-content">
    <?php

    if (is_user_logged_in ()) {
        if(isset($wp_query->query_vars['user_id'])) {
            $cur_user_id = urldecode($wp_query->query_vars['user_id']);
        }

        global $wpdb;
        $key = $wpdb->get_results("SELECT `private_key` FROM `wp_key`");

        $data_adress = get_user_meta($cur_user_id,'user_adress',true);
        $data_phone = get_user_meta($cur_user_id,'user_phone',true);

        $pk = openssl_get_privatekey($key[0]->private_key);
        openssl_private_decrypt(base64_decode($data_adress), $out, $pk);
        openssl_private_decrypt(base64_decode($data_phone), $out_phone, $pk);



        if( is_user_logged_in () && is_singular() && $cur_user_id) {

            echo '<p> Nickname: '. get_user_meta($cur_user_id,'nickname',true) . '</p>';
            echo '<p> Имя: '. get_user_meta($cur_user_id,'first_name',true) . '</p>';
            echo '<p> Фамилия: '. get_user_meta($cur_user_id,'last_name',true) . '</p>';
            echo '<p> Адрес: '. $out . '</p>';
            echo '<p> Телефон: '. $out_phone . '</p>';
            if ( get_user_meta($cur_user_id,'family_group',true) == '1') {
                echo '<p> Семейное положение: Не женат/ не замужем</p>';
            } else if(get_user_meta($cur_user_id,'family_group',true) == '2') {
                echo '<p> Семейное положение: Женат / замужем</p>';
            }
            echo '<p> Пол: '. get_user_meta($cur_user_id,'user_gender',true) . '</p>';

        };
    }
?>
		</main><!-- #main -->
	</section><!-- #primary -->
<?php
get_footer();
