<?php

require($_SERVER['DOCUMENT_ROOT'] . '/init.php');

if (!isset($_COOKIE['REMEMBERME'])) {
    redirect('/?logout');
}

list($user, $leerling, $token, $mac) = explode(':', $_COOKIE['REMEMBERME']);

$user = clean_data($user);

$query =
    "SELECT
        token,
        created,
        days_valid
    FROM
        tokens
    WHERE
        user='{$user}' AND token='{$token}' AND type = 'remember_me'";

$tokens_sql = sql_query($query, false);

$valid_cookie = false;

if ($tokens_sql->num_rows > 0) {
    while ($token_sql = $tokens_sql->fetch_assoc()) {
        $valid_date = false;
        $valid_hmac = false;
        $valid_hash = false;

        if ($token_sql['created'] < time()-$token_sql['days_valid']*24*60*60) {
            $valid_date = true;
        }

        if (hash_equals($token_sql['token'], $token)) {
            $valid_hash = true;
        }

        if (hash_equals(hash_hmac('sha512', $user . ':' . $leerling . ':' . $token_sql['token'], $GLOBALS['config']->security->hmac), $mac)) {
            $valid_hmac = true;
        }

        if ($valid_date && $valid_hmac && $valid_hash) {
            $valid_cookie = true;
            break;
        }
    }
}

if (!$valid_cookie) {
    if (!empty($token)) {
        $query =
            "DELETE FROM
                tokens
            WHERE
                user='{$user}' AND token='{$token}' AND type = 'remember_me'";
        sql_query($query, false);
    }

    redirect('/?logout');
}

$table = ($leerling == 1) ? 'leerlingen' : 'docenten';

$query =
    "SELECT
        id,
        class,
        active,
        password,
        first_name,
        last_name,
        failed_login,
        admin
    FROM
        {$table}
    WHERE
        id='{$user}'";

$user = sql_query($query, true);

if ($user['failed_login'] > 4) {
    log_action('user.account_blocked', $user['first_name'] . ' ' . $user['last_name']);
    redirect('/?reset', 'Uw account is geblokkeerd door teveel mislukt inlogpogingen, contacteer AUB de administrator.');
}

if (!$user['active']) {
    log_action('user.disabled', $user['first_name'] . ' ' . $user['last_name']);
    redirect('/?reset', 'Uw account is niet actief, contacteer AUB de administrator.');
}

sql_query("UPDATE {$table} SET failed_login='0' WHERE id='{$user['id']}' AND class='{$user['class']}'", false);

log_action('user.cookie_auth_succeeded', $user['first_name'] . ' ' . $user['last_name']);
log_action('user.login', $user['first_name'] . ' ' . $user['last_name']);

$return_url = $_SESSION['return_url'];

session_destroy();
session_start();

$_SESSION['logged_in'] = true;
$_SESSION['ip'] = ip();
$_SESSION['id'] = $user['id'];
$_SESSION['class'] = $user['class'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];

if (isset($user['admin'])) {
    $_SESSION['admin'] = $user['admin'];
}

session_regenerate_id(true);

remember_clear_old();

if (!empty($return_url)) {
    redirect($return_url, 'U bent ingelogd');
} else {
    redirect('/general/home', 'U bent ingelogd');
}
