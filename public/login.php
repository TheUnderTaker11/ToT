<?php

/**
 * login.php
 *
 * Handle user authentication
 *
 * The basic process for this page is to:
 * - determine existing session
 *   - logout or
 *   - redirect to front page
 * - accept credentials and
 *   - start a session or
 *   - show an error
 * - show the login form
 *
 * @author Clay Bellou (Derived from DRyft login.php by Errol Sayre)
 */


namespace ToT;

require_once('../bootstrap.php');

// determine if there is a session
$user = Session::getSession()->getUser();
$error = null;
// determine if a username has been submitted
if (
    array_key_exists('user', $_REQUEST) &&
    array_key_exists('password', $_REQUEST)
) {
    // try to validate the provided password
    try {
        $user = User::getUserByName($_REQUEST['user']);
        if ($user->validatePassword($_REQUEST['password'])) {

            // setup the session
            Session::getSession()->setupSession($user);
        } else {
            $error = 'Unable to login: incorrect password.';
            unset($user);
        }
    } catch (Database\Exception $e) {
        $error = 'Unable to locate user: ' . $e->getMessage();
        unset($user);
    }
}

// Setup a shortcut to our application path
$linker = new Linker;

if (array_key_exists('logout', $_REQUEST)) {
    Session::destroy();
    $user = null;
    $message = 'Logged out successfully.';
} elseif ($user) {

    $message = 'Session started.';
    // redirect the user to the main page
    header('Location: ' . $linker->urlPath() . 'redirectToIndex.php');
} elseif (array_key_exists('login', $_REQUEST)) {
    $message = 'Unable to log in.';
} else {
    $message = 'Please login';
}

if (array_key_exists('createAccount', $_REQUEST)) {
    header('Location: ' . $linker->urlPath() . 'createAccount.php');
}


// now that actions that modify headers are complete, start the page template

// add HTML head
include '../head.html';

// Output a page title and any other specific head elements
echo '		<title>Please login | ToT</title>' . PHP_EOL;


//Now Display Login Form

?>
<div class="d-flex justify-content-center">
    <h1><?= $message ?></h1>
    <br>
    <?php
    if ($error) {
    ?>
        <p class="error"><?= $error ?></p>
    <?php
    }
    ?>
</div>

<div class="d-flex justify-content-center">
    <form method="POST" action="<?= $linker->urlPath() ?>login.php">
        <fieldset>
            <legend>Username</legend>
            <input type="text" name="user">
        </fieldset>
        <fieldset>
            <legend>Password</legend>
            <input type="password" name="password">
        </fieldset>
        <input class="btn btn-success" type="submit" name="login" value="Login" style="margin-top: 5px">
        <input class="btn btn-success" type="submit" name="createAccount" value="New Account" style="margin-top: 5px">
    </form>
</div>
<?php

// add page footer
include '../footer.html';
