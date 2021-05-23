<?php
namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

if(!$user){
    header('Location: ' . $linker->urlPath() . 'login.php');
}elseif($user->isCustomer()){
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
}
}elseif($user->isHost()){
    header('Location: ' . $linker->urlPath() . 'hostIndex.php');
}
}elseif($user->isWaiter()){
    header('Location: ' . $linker->urlPath() . 'waitStaffIndex.php');
}
else{
    if(isset($_GET['id'])){
    $id = $_GET['id'];
    $account = User::getUserById($id);
    $account->deleteUserByUserID($id);

    header('Location: ' . $linker->urlPath() . 'employees.php');
    }
    else{
        header('Location: ' . $linker->urlPath() . 'employees.php');   
    }
}