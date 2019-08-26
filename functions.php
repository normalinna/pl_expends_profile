<?php

function ext_profile_change_user_field() {
    if(!is_user_logged_in ()) return;

    global $user_id;
    $key_a = 'user_adress';
    $user_adress = get_user_meta( $user_id, $key_a, true );

    $key_p = 'user_phone';
    $user_phone = get_user_meta( $user_id, $key_p, true );

    $key_f = 'family_group';
    $family_group = get_user_meta( $user_id, $key_f, true );

    $key_g = 'user_gender';
    $user_gender = get_user_meta( $user_id, $key_g, true );


    ?>
    <h2>Дополнительные данные учетной записи</h2>
    <table class="form-table">
        <tbody>

        <tr class="user_adress-wrap">
            <th><label for="adress">Адрес</label></th>
            <td>
                <input type="text" name="user_adress" value="<?php echo $user_adress ?>" style="width: 25em"><br>
            </td>
        </tr>

        <tr class="user_phone-wrap">
            <th><label for="phone">Телефон</label></th>
            <td>
                <input type="text" name="user_phone" value="<?php echo $user_phone ?>" style="width: 25em"><br>
            </td>
        </tr>

        <tr class="user_phone-wrap">
            <th><label for="family_group">Семейное положение</label></th>
            <td>
                <select name="family_group" id="family_group">
                    <option value="1" <?php selected( $family_group[0], "1" ); ?>>Не женат/ не замужем</option>
                    <option value="2" <?php selected( $family_group[0], "2" ); ?>>Женат / замужем</option>
                </select>
            </td>
        </tr>

        <tr class="user_gender-wrap">
            <th><label for="gender">Пол</label></th>
            <td style="width: 50em">
                <input type="radio" name="gender" id="male" <?php if ($user_gender == 'female' ) { ?>checked="checked"<?php }?> value="female"><p>Мужской пол</p>
            </td>
            <td style="width: 50em">
                <input type="radio" name="gender" id="female" <?php if ($user_gender == 'male' ) { ?>checked="checked"<?php }?> value="male"><p>Женский пол</p><br>
            </td>
        </tr>

        </tbody>
    </table>
    <?php
}

//выводит список пользователей на страницу
function ext_profile_content($content) {

    if( $GLOBALS['post']->post_name == 'all_users' && is_user_logged_in()) {

        $users = get_users();
        $total_users = count($users);

        echo '<ul id="users">';
        foreach($users as $q) { ?>
            <?php $user_info = get_userdata($q->ID)?>

            <li class="user clearfix">
                <!-- Аватар пользователя -->
                <div class="user-avatar">
                    <?php echo get_avatar( $q->ID, 80 ); ?>
                </div>

                <div class="user-data">
                    <!-- Имя -->
                    <h4 class="user-name">
                        <a href="<?php echo untrailingslashit(get_permalink(get_page_by_path( 'profile' ))). '?user_id='. $q->ID;?>">
                            <?php echo $user_info->nickname;?>
                        </a>
                    </h4>
                </div>
            </li>
        <?php }
        echo '</ul>';
        echo '<p> Всего пользователей:' .$total_users. '</p>';

    };
    return $content;
}
//обновить данные пользователя
function ext_profile_change_user_update() {
    global $user_id;
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;


    $pub = "-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAJ3IXnRauNZ7fPgpMJJEQlvzhr3aB2tQ
+ficNu6YCgx1CK/W16T3iGQ6f3SvBwkQwEKtQ2oy9xJ/n36eZ4Lyz88CAwEAAQ==
-----END PUBLIC KEY-----";

    $pk  = openssl_get_publickey($pub);

    openssl_public_encrypt($_POST['user_adress'], $encrypted, $pk);
    openssl_public_encrypt($_POST['user_phone'], $in, $pk);

    $pub_a = chunk_split(base64_encode($encrypted));
    $pub_p = chunk_split(base64_encode($in));

    $key_a = 'user_adress';
    $key_g = 'user_gender';
    $key_p = 'user_phone';
    $key_f = 'family_group';


    update_user_meta( $user_id, $key_a, $_POST['user_adress'] );
    update_user_meta( $user_id, $key_p, $_POST['user_phone'] );
    update_user_meta( $user_id, $key_f, $_POST['family_group'] );
    update_user_meta( $user_id, $key_g, $_POST['gender'] );

    update($user_id, 'user_adress', $pub_a );
    update($user_id, 'user_phone', $pub_p);
}

function ext_profile_add_script() {
    if(!is_user_logged_in ()) return;
    wp_enqueue_script('ext-profile-script', plugin_dir_url(__FILE__) . 'js/ext-profile-script.js', array('jquery'), null, true);
    wp_enqueue_style('ext-profile-style', plugin_dir_url(__FILE__) . 'css/ext-profile-style.css');
}


function ext_profile_new_query_vars($vars)
{
    $vars[] = 'user_id';
    return $vars;
}

function ext_profile_when_rewrite_rules( $wp_rewrite ) {
    $new_rules = array();
    $new_rules['category/tasks/(\d*)$'] = 'index.php?when=$matches[1]';
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

function ext_profile_page_template($template) {
    if( is_page () == 'page-profile' ){
        return wp_normalize_path( WP_PLUGIN_DIR ) . '/pl_extends_profile/page-profile.php';
    }
    return $template;
}

function ext_profile_category() {
    require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
    $cat_name = array(
        'cat_name' => 'Users',
        'category_description' => 'список пользователей',
        'category_nickname' => 'users'
    );


    $cat_id = wp_insert_category( $cat_name );

    if( $cat_id )
        echo 'Категория добавлена';
    else
        echo 'Не удалось добавить категорию';
}

function ext_profile_post() {

    if (!post_exists('all_users')) {
        $post_title = 'all_users';
        $post_content = 'Все пользователи';

        $new_post = array(
            'post_author' => 'admin',
            'post_content' => $post_content,
            'post_title' => $post_title,
            'post_status' => 'publish'
        );

        $post_id = wp_insert_post($new_post);

        wp_set_object_terms( $post_id, 5, 'all_users' );
        wp_set_post_categories($post_id ,'users', true);
    }
}


function get_meta_keys_to_encrypt(){
    return array('user_adress', 'user_phone', 'user_gender', 'family_group' );
}

function update( $user_id, $meta_key, $meta_value, $prev_value = '') {

    return update_metadata( 'user', $user_id, $meta_key, $meta_value, $prev_value );
}
