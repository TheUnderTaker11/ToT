<?php

/**
 * testpage.php
 *
 * Small example page showing how to use the Database/Class setup for the ToT group!
 * 
 * 
 * @author Clay Bellou
 */

namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

//Before outputting any HTML, ensure you handle all POST/redirects/etc. that would require redirecting before the user even sees the page.

//Must be logged in to view this page.
if (!$user) {
    //Example of using linker to go to the login page
    header('Location: ' . $linker->urlPath() . 'login.php');
}
/////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';


echo '<h1> This is a test/example page! </h1>';

if (array_key_exists('logout', $_POST)) {
    logout($linker);
}
function logout($linker)
{
    Session::getSession()->destroy();
    header('Location: ' . $linker->urlPath() . 'login.php');
}

if ($user->isCustomer()) {
    echo '<h1> Hello Customer! (Log in as an Employee to see a list of all customers)</h1>';
?>
    <form method="post">
        <input type="submit" name="logout" class="button" value="Log Out" />
    </form>
<?php
} else {
    echo '<h1> Hello Employee! Going to now show a list of all customers. </h1>';
    //Through a method within the User class, get the list of all customer users from the DB.
    $customers = User::getCustomers();
?>
    <br />
    <br />
    <h1>Assigned but Unfinished Rides</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $item) {
            ?>
                <tr>
                    <td><?= $item->id() ?></td>
                    <td><?= $item->username() ?></td>
                    <td><?= $item->firstName() ?> <?= $item->lastName() ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <form method="post">
        <input type="submit" name="logout" class="button" value="Log Out" />
    </form>
<?php
}

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
