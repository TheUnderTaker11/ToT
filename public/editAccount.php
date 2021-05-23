<?php
namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

//Must be logged in to view this page.
/*
if (!$user) {
    //Example of using linker to go to the login page
    header('Location: ' . $linker->urlPath() . 'login.php');
}
*/

if(isset($_POST['save'])){
    if($user->isCustomer()){
        $id = $user->id();
        $account = User::getUserById($id);
        $account->setUsername($_POST['username']);
        $account->setType($_POST['userType']);
        //$account->password = $_POST['password'];
        //$passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $account->setFirstName($_POST['firstName']);
        $account->setLastName($_POST['lastName']);
        $account->setEmail($_POST['email']);
        $account->setPhone($_POST['phone']);
        
        $account->save();
        header('Location: ' . $linker->urlPath() . 'testpage.php');
    }else{
        $id = $_GET['id'];
        $account = User::getUserById($id);
        $account->setUsername($_POST['username']);
        $account->setType($_POST['userType']);
        //$account->password = $_POST['password'];
        //$passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $account->setFirstName($_POST['firstName']);
        $account->setLastName($_POST['lastName']);
        $account->setEmail($_POST['email']);
        $account->setPhone($_POST['phone']);
        
        $account->save();
        header('Location: ' . $linker->urlPath() . 'employees.php');
    }

}
elseif(!isset($_GET['id'])){
    header('Location: ' . $linker->urlPath() . 'employees.php');
}
else{
    //Logout 
    if (array_key_exists('logout', $_POST)) {
        Session::getSession()->destroy();
        $user = null;
        header('Location: ' . $linker->urlPath() . 'login.php');
    }

/////////////////Begin HTML output//////////////////////
include '../head.html';
include '../header2.html';
?>

</head>
<body class="bg-light"> 
    <div class="container">
        <main>
            <?php 
                if($user->isManager()){
                    $id = $_GET['id'];
                    $account = User::getUserById($id);
            ?>
            <div class="d-flex justify-content-center">
                <h1>Edit <?php echo $account->firstName(); echo " "; echo$account->lastName() ?>'s Information</h1>
            </div>
            <div class="d-flex justify-content-center">
                <!-- do i need to include foreach? -->
                <form id="updateAccountForm" method="POST">
                    <div class="form-group d-none">
                        <label for="id">ID</label>
                        <input type="text" name="id" class="form-control" value="<?php echo $account->id()?>" id="id">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $account->username()?>" id="userName">
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-select" aria-label="Select User Type" name="userType" id="type" value="<?php echo $account->type()?>" required>
                            <option value="Manager" <?php if($account->type() == "Manager"){ echo "selected";}?>>Manager</option>
                            <option value="Waiter" <?php if($account->type() == "Waiter"){ echo "selected";}?>>Waiter</option>
                            <option value="Host" <?php if($account->type() == "Host"){ echo "selected";}?>>Host</option>
                        </select>
                    </div>
                    <!--
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" name="password" class="form-control" value="<?php //echo $user->password()?>" id="passwordInput">
                    </div>
                    An element to toggle between password visibility 
                    <input type="checkbox" onclick="showHidePassword()">Show Password-->
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" class="form-control" value="<?php echo $account->firstName()?>" id="firstName">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" class="form-control" value="<?php echo $account->lastName()?>" id="lastName">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $account->email()?>" id="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $account->phone()?>" id="phone">
                    </div>
                    <br />
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-success" name="save">Save</button>
                    </div> 
                </form>
            </div>
        <?php 
            }
        elseif($user->isCustomer()){
            $account = User::getUserById($user->id());
        ?>
            <div class="d-flex justify-content-center">
                <h1>Edit <?php echo $account->firstName(); echo " "; echo$account->lastName()?>'s Information</h1>
            </div>
            <div class="d-flex justify-content-center">
                <!-- do i need to include foreach? -->
                <form id="updateAccountForm" method="POST">
                    <div class="form-group d-none">
                        <label for="id">ID</label>
                        <input type="text" name="id" class="form-control" value="<?php echo $account->id()?>" id="id">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $account->username()?>" id="userName">
                    </div>
                    <div class="form-group d-none">
                        <label for="userType">Type</label>
                        <input type="text" name="userType" class="form-control"  id="userType" value="Customer">
                    </div>
                    <!--
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" name="password" class="form-control" value="<?php //echo $user->password()?>" id="passwordInput">
                    </div>
                    An element to toggle between password visibility 
                    <input type="checkbox" onclick="showHidePassword()">Show Password-->
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" class="form-control" value="<?php echo $account->firstName()?>" id="firstName">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" class="form-control" value="<?php echo $account->lastName()?>" id="lastName">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $account->email()?>" id="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $account->phone()?>" id="phone">
                    </div>
                    <br />
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-success" name="save">Save</button>
                    </div> 
                </form>
            </div>     
    <?php
        }
        else{
            header('Location: ' . $linker->urlPath() . 'testpage.php');
        }
    ?>
<?php
// add page footer  
include '../footer.html';
}
?>

<!-- show/hide password 
<script>
    function showHidePassword() {
    var x = document.getElementById("passwordInput");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
-->