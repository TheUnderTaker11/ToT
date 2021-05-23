<?php

/**
 * redirectToIndex.php
 *
 * Forwards user to index based on type
 * 
 */

namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

//Must be logged in to view this page.
if (!$user) {
    header('Location: ' . $linker->urlPath() . 'login.php');
}

if ($user->isCustomer()) {
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
} elseif ($user->isHost()) {
    header('Location: ' . $linker->urlPath() . 'hostIndex.php');
} elseif ($user->isManager()) {
    header('Location: ' . $linker->urlPath() . 'hostIndex.php');
} elseif ($user->isWaiter()) {
    header('Location: ' . $linker->urlPath() . 'waitstaffIndex.php');
} else { // Fall through for user type error
    header('Location: ' . $linker->urlPath() . 'testpage.php');
}
