<?php

add_action('template_redirect', function () {

    if (!is_user_logged_in()) {
        wp_redirect(site_url('/wp-login'));
        exit();
    }

});

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), false, 'all');

    wp_enqueue_style('main-sass', get_template_directory_uri() . '/build/css/main.css', array(), '1.0', 'all');

});

// birthday app logo

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/full-logo.png);
            height:200px;
            width:200px;
            background-size: 200px 200px;
            background-repeat: no-repeat;
            position: relative;
            top: -15px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return '';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function my_login_stylesheet() {
    wp_enqueue_style('main-sass', get_template_directory_uri() . '/build/css/main.css', array(), '1.0', 'all');
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

// ********************
// birthday field start

/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */

function wporg_usermeta_form_field_birthday($user)
{
    ?>
    <h3>It's Your Birthday</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="birthday">Birthday</label>
            </th>
            <td>
                <input type="date"
                       class="regular-text ltr"
                       id="birthday"
                       name="birthday"
                       value="<?=esc_attr(get_user_meta($user->id, 'birthday', true));?>"
                       title="Please use YYYY-MM-DD as the date format."
                       pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"
                       max=<?=date('Y-m-d')?>
                       min = <?php $min = new DateTime(); echo $min->modify('-100 years')->format('Y-m-d')?>
                       required>
                <p class="description">
                    Please enter your birthday date.
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function wporg_usermeta_form_field_birthday_update($user_id)
{
    // check that the current user has the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    $min = new DateTime();
    $min = $min->modify("-100 years")->format('Y-m-d');

    if($_POST['birthday'] > date('Y-m-d') || $_POST['birthday'] < $min) {
        echo "Please enter a valid date!";
        exit();
    }
    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthday',
        $_POST['birthday']
    );
    

}

// add the field to user's own profile editing screen
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_birthday'
);

// add the field to user profile editing screen
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_birthday'
);

// add the save action to user's own profile editing screen update
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_birthday_update'
);

// add the save action to user profile editing screen update
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_birthday_update'
);

// ********************
// birthday field end

// get users' upcoming birthdays

// function my_user_sort( $query_args ){

//     $query_args->query_vars['orderby'] = 'birthday';
//     return $query_args;
// }
// add_action( 'pre_get_users', 'my_user_sort' );

?>
