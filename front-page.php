<?php get_header(); ?>

<?php   
        // choose your favorite birthday quotes and be suprised each day with a new one!
        $quotes = [
            'All the world is birthday cake, so take a piece, but not too much!',
            'You’re older today than yesterday but younger than tomorrow, happy birthday!',
            'You are only young once, but you can be immature for a lifetime. Happy birthday!',
            'There are two great days in a person’s life — the day we are born and the day we discover why.',
            'Happy moments. Happy thoughts. Happy dreams. Happy feelings. Happy birthday.',
            'Celebrate your birthday today. Celebrate being happy every day.',
            'The more you praise and celebrate your life, the more there is in life to celebrate.'
        ];
        $quote = array_rand($quotes);
    ?>
<!-- Container Start -->
<div class="container-fluid birthday-front-page pb-2">
    <div class="row banner py-3 mb-2">
        <div class="col birthday-confetti d-flex justify-content-center align-items-center">
            <?php
                    $i = 0;
                    while($i <= 15){
                        ?>
            <div class="confetta"></div>
            <?php
                        $i++;
                    }
                ?>
            <img src="<?= get_stylesheet_directory_uri();?>/assets/img/banner-logo.png" class="banner-logo pr-2 text-center"
                alt="banner-logo">
            <div class="birthday-slogan text-center">
                <?= $quotes[$quote] ?>
            </div>

        </div>
    </div>
    <?php
            // multisort birthday arrays
            function array_orderby()
            {
                $args = func_get_args();
                $data = array_shift($args);
                foreach ($args as $n => $field) {
                    if (is_string($field)) {
                        $tmp = array();
                        foreach ($data as $key => $row)
                            $tmp[$key] = $row[$field];
                        $args[$n] = $tmp;
                        }
                }
                $args[] = &$data;
                call_user_func_array('array_multisort', $args);
                return array_pop($args);
            }


            $users = get_users('meta_key=birthday');

            $today = new DateTime();
            foreach ( $users as $user ) {
                // get birthdays in current year
                $birthday = get_user_meta( $user->id, 'birthday', true );
                $timestamp = strtotime($birthday);
                $user->birthdayTime= new DateTime($birthday);
                $user->currBday =  $user->birthdayTime + $user->birthdayTime->diff($today)->y;
                $user->newBday = new DateTime(date('Y-m-d', strtotime(+ $user->currBday . 'years', strtotime($birthday))));
                if ($user->newBday < $today)
                {
                    $$user->currBday =  $user->birthdayTime + $user->birthdayTime->diff($today)->y + 1;
                    $user->newBday = date('Y-m-d', strtotime(+ $user->currBday . 'years', strtotime($birthday)));

                }
            }

            // sort birthdays
            $sorted_users = [];
            foreach($users as $u)
            {
                array_push($sorted_users,get_object_vars($u->data));
            }
            
            $sorted_users = array_orderby($sorted_users, 'newBday', SORT_ASC);
            foreach ( $sorted_users as $user ) {
                ?>
    <div class="row birthday-list">
        <?php
                        $avatar = get_avatar_url($user['ID']);
                        $firstName = get_user_meta( $user['ID'], 'first_name', true );
                        $lastName = get_user_meta( $user['ID'], 'last_name', true );
                        $birthday = get_user_meta( $user['ID'], 'birthday', true );
                        $birthdayFormat = date("d/m/Y", strtotime($birthday));
                        $yearsOld = $user['currBday'];
                    ?>
        <div class="col-lg-3 col-sm-12 col-xs-12"><img src="<?= $avatar ?>" alt="user-avatar" class="rounded-circle"></div>
        <div class="col-lg-9 col-sm-12 col-xs-12 info-text">
            <p><i class="fas fa-birthday-cake frame blue-bg"></i> &nbsp;<b>
                    <?= $firstName ?>
                    <?= $lastName ?></b> is turning
                <?= $yearsOld ?> this year!</p>
            <p><i class="fas fa-calendar-day frame yellow-bg"></i> &nbsp; The big date was on
                <?= $birthdayFormat ?>.</p>
        </div>
    </div>
    <?php }?>
</div>
<!-- Container End -->
<?php get_footer(); ?>