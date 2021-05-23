<?php
namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

//Example of using linker to go to the login page
//header('Location: ' . $linker->urlPath() . 'login.php');

if(!$user){
    header('Location: ' . $linker->urlPath() . 'login.php');
}elseif($user->isCustomer()){
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
}elseif($user->isHost()){
    header('Location: ' . $linker->urlPath() . 'hostIndex.php');
}elseif($user->isWaiter()){
    header('Location: ' . $linker->urlPath() . 'waitStaffIndex.php');
}else{

//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}
/////////////////Begin HTML output//////////////////////
include '../head.html';    
include '../header2.html';
$users = User::getEmployees();

?>
<div class="d-flex justify-content-center">
    <table class="table table-sm">
    <thead>
        <tr>
        <th scope="col" class="d-none">ID</th>
        <th scope="col">Username</th>
        <th scope="col">Account Type</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email</th>
        <th scope="col">Phone</th>
        <th scope="col">Edit</th>
        <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $employees){?>
        <tr>
        <td class="d-none"><?php echo $employees->id()?></td>            
        <td><?php echo $employees->username()?></td>
        <td><?php echo $employees->type()?></td>
        <td><?php echo $employees->firstName()?></td>        
        <td><?php echo $employees->lastName()?></td>
        <td><?php echo $employees->email()?></td>
        <td><?php echo $employees->phone()?></td>
        <td><a class="btn btn-primary" name="editAccount" href="editAccount.php?id=<?php echo urlencode($employees->id());?>">Edit</a><?php?></td>
        <td><form method="POST" action="deleteEmployee.php"><a class="btn btn-danger" type="submit" name="deleteAccount" href="deleteEmployee.php?id=<?php echo urlencode($employees->id());?>">Delete</a></form></td>
        </tr>
        <?php } ?>
    </tbody>
    </table>
</div>   
<div class="d-flex justify-content-center"> 
    <a class="btn btn-success" name="createAccount" href="createAccount.php">Create Account</a>
</div>
<?php 

// add page footer  
include '../footer.html';
}
?>