<?php

/**
 * waitstaffIndexIndex.php
 */

namespace ToT;

require_once('../bootstrap.php');

//Get the User object for this session (Found in "ToT/User.php")
$user = Session::getSession()->getUser();

//Linker to ensure correct URL paths.
$linker = new Linker;

//Must be logged in to view this page.
if (!$user) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}
//Must be Host or Manager to view this page
if(!($user->isWaiter() || $user->isManager())){
    logout($linker);
}
if (array_key_exists('back', $_POST)) {
    header('Location: ' . $linker->urlPath() . 'waitstaffIndex.php');
}


//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}

if (array_key_exists('viewReservation', $_POST)) {
    //list all customers tied to reservation
    //have button next to each to see order
    //do same trick as in hostIndex   $_POST[$_POST['viewReservation']]

    $all_orders_in_reservation = Order::getOrdersByReservationId($_POST['viewReservation']);
    include '../head.html';
    include '../header2.html';

    echo '<h1> Reservation Information </h1>';
    echo '<br>';
    echo '<h3> Customers </h3>';
    ?>
     <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Order</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_orders_in_reservation as $order) {
                $customer = User::getUserById($order->userID());
                $fullName = $customer->firstName()." ".$customer->lastName();
            ?>
                <tr>
                    <td><?= $fullName ?></td>
                    <td><button type='submit' name='viewOrder' value="<?=  $order->id()  ?>">View Order</button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <form method="post">
        <input type="submit" name="back" class="btn btn-primary" value="Back" />
    </form>

    
    <?php
}else{

    if($user->isManager()){
        $all_assigned_reservations = Reservation::getAssignedReservations(); 
        /////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
include '../header2.html';

echo '<h1> Manager Reservations Console </h1>';
echo '<br>';
echo '<h3> Assigned Reservations </h3>';
?>
 <table class="table table-striped">
        <thead>
            <tr>
                <th>Party Representative</th>
                <th>Reservation</th>  
            </tr>
        </thead>
        <tbody>
        <form method="post">
            <?php foreach ($all_assigned_reservations  as $reservation) {
                $customer = User::getUserById($reservation->useID());
                $fullName = $customer->firstName()." ".$customer->lastName();
                //same code as in getting assigned reservation (use query to see if all orders are completed)
                $all_orders = Order::getOrdersByReservationId($reservation->resID());
                $is_complete=TRUE;
                foreach ($all_orders  as $order) {
                    //see if at least one order is not completed 
                    //if so then still display reservation tied to order
                    if($order==NULL){
                        
                    }else if($order->complete()==FALSE){
                        $is_complete=false;
                    }
                }
                if($is_complete==false || $all_orders == NULL){

                
            ?>
                <tr>
                    <td><?= $fullName ?></td>
                    <td><button type='submit' name='viewReservation' value="<?=  $reservation->resID()  ?>">View Reservation</button></td><!--add value to pass something -->
                </tr>
            <?php }} ?>
        </form>
        </tbody>
    </table>







<?php

    }else{

    

//same code as in getting assigned reservation (use query to see if all orders are completed)
$all_assigned_reservations = Reservation::getReservationsByWaiterID($user->id()); //chnage to get assigned reservations by wait id


/////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
include '../header2.html';

echo '<h1> Waitstaff Console </h1>';
echo '<br>';
echo '<h3> Assigned Reservations </h3>';
?>
 <table class="table table-striped">
        <thead>
            <tr>
                <th>Party Representative</th>
                <th>Reservation</th>  
            </tr>
        </thead>
        <tbody>
        <form method="post">
            <?php foreach ($all_assigned_reservations  as $reservation) {
                $customer = User::getUserById($reservation->useID());
                $fullName = $customer->firstName()." ".$customer->lastName();
                //same code as in getting assigned reservation (use query to see if all orders are completed)
                $all_orders = Order::getOrdersByReservationId($reservation->resID());
                $is_complete=TRUE;
                foreach ($all_orders  as $order) {
                    //see if at least one order is not completed 
                    //if so then still display reservation tied to order
                    if($order==NULL){
                        
                    }else if($order->complete()==FALSE){
                        $is_complete=false;
                    }
                }
                if($is_complete==false || $all_orders == NULL){

                
            ?>
                <tr>
                    <td><?= $fullName ?></td>
                    <td><button type='submit' name='viewReservation' value="<?=  $reservation->resID()  ?>">View Reservation</button></td><!--add value to pass something -->
                </tr>
            <?php }} ?>
        </form>
        </tbody>
    </table>







<?php
}}
?>



<!-- Logout Button -->
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>



<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
