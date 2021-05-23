<?php
namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();
//Linker to ensure correct URL paths.
$linker = new Linker;

//Example of using linker to go to the login page
//header('Location: ' . $linker->urlPath() . 'login.php');
$db = Database\Connection::getConnection();
if(isset($_POST['submit'])){

        $username = $_POST['username'];
        $type = $_POST['userType'];
        $password = $_POST['password'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $checkUsername = mysqli_query($db, "SELECT username from users WHERE username='$username'");
        $result = mysqli_num_rows($checkUsername);
        if($result > 0){
            echo '<script>alert("This username already exists. Please enter a different username. Please wait to be redirected.")</script>';
            header( "refresh:1; url=createAccount.php" ); //wait for 1 seconds before redirecting
        }else{
            $query = "INSERT INTO users(username, type, pw_hash, name_first, name_last, email, phone) VALUES ('$username', '$type', '$passwordHash', '$firstName', '$lastName', '$email', '$phone')";
            mysqli_query($db, $query);
            if(!$user){
                echo '<script>alert("Account created successfully. Log in with your new credentials. Please wait to be redirected.")</script>';
                header( "refresh:1; url=login.php" ); //wait for 1 seconds before redirecting               
            }
            else{
                header('Location: ' . $linker->urlPath() . 'employees.php');
            }
        }    
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

//If user is not logged in, display this form.
if (!$user) {         
        ?>

            <div class="d-flex justify-content-center">
                <h1>Create Account</h1>
            </div>
            <!-- perhaps do an if user -> admin then different html -->
            <div class="d-flex justify-content-center">
                <!-- do i need to include foreach? -->
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" id="username" required>
                    </div>
                    <div class="form-group d-none">
                        <label for="userType">Type</label>
                        <input type="text" name="userType" class="form-control"  id="userType" value="Customer">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                    </div>
                    <!-- An element to toggle between password visibility 
                    <input type="checkbox" onclick="showHidePassword()">Show Password-->
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" class="form-control" placeholder="First name" id="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" class="form-control" placeholder="Last name" id="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone number" id="phone" required>
                    </div>
                    <br />
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>
                    </div> 
                </form>
            </div>
        <?php
    }
        

elseif($user->isManager()){ ?>
                <div class="d-flex justify-content-center">
                    <h1>Create Account</h1>
                </div>
                <!-- perhaps do an if user -> admin then different html -->
                <div class="d-flex justify-content-center">
                    <!-- do i need to include foreach? -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username" id="username" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-select" aria-label="Select User Type" name="userType" id="type" required>
                                <option selected value="">Select Account Type</option>
                                <option value="Manager">Manager</option>
                                <option value="Waiter">Waiter</option>
                                <option value="Host">Host</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                        </div>
                        <!-- An element to toggle between password visibility 
                        <input type="checkbox" onclick="showHidePassword()">Show Password-->
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" name="firstName" class="form-control" placeholder="First name" id="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" name="lastName" class="form-control" placeholder="Last name" id="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="Phone number" id="phone" pattern="^\d{3}-\d{3}-\d{4}$" required>
                            <small id="phoneHelp" class="form-text text-muted">Enter phone as 123-456-7890.</small>
                        </div>
                        <br />
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="btn btn-success" name="submit" id="submit">Submit</button>
                        </div> 
                    </form>
                </div>
    
<?php 
    }
    else{
        header('Location: ' . $linker->urlPath() . 'testpage.php');
    }

        // add page footer  
        include '../footer.html';
    }
        ?> 

<!-- show/hide password 
<script>
    function showHidePassword() {
    var x = document.getElementById("password");
        if (x.type == "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
-->