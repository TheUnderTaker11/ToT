<?php

/**
 * hostIndex.php
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
if(!($user->isHost() || $user->isManager())){
    logout($linker);
}

//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}

if (array_key_exists('assign', $_POST)) { //create / destroy order upon assigning them
  
    Reservation::assignReservation($_POST['assign'], $_POST[$_POST['assign']]);
}
if (array_key_exists('unassign', $_POST)) {
  
    Reservation::assignReservation(NULL, $_POST['unassign']);
}

$all_wait_staff = User::getWaiters();
$all_unassigned_reservations = Reservation::getUnassignedReservations();
$all_assigned_reservations = Reservation::getAssignedReservations();

/////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
include '../header2.html';
?>


<!--Start Display -->

<?php
echo '<h1> Host Console </h1>';
echo '<br>';


echo '<h3> Available Waitstaff </h3>';
?>
 <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_wait_staff as $waiter) {
                $fullName = $waiter->firstName()." ".$waiter->lastName();
            ?>
                <tr>
                    <td><?= $fullName ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>
    <h3> All Unassigned Reservations </h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Reservation ID</th>
                <th>Assign</th>

            </tr>
        </thead>
        <tbody>
        
            <?php foreach ($all_unassigned_reservations as $reservation) {
                $customer = User::getUserById($reservation->useID());
                $fullName = $customer->firstName()." ".$customer->lastName();
            ?><form method="post">
                <tr>
                    <td><?=  $fullName ?></td>
                    <td><?= $reservation->resID() ?></td>
                    <td> <select type="select" name="assign" class="text" onchange='this.form.submit()'>
                        <option></option>
                        <?php foreach ($all_wait_staff as $waiter) {  ?>
                                <option value="<?= $waiter->id() ?>"><?= $waiter->firstName()." ".$waiter->lastName() ?></option>
                        <?php } 
                         ?>
                    </select>
                    <?php foreach ($all_wait_staff as $waiter) {  ?>
                                <input type="hidden" name="<?= $waiter->id() ?>" value="<?= $reservation->resID() ?>"/>
                        <?php } 
                         ?>  
                
                </td>
                </tr></form>
            <?php } ?>
        
        </tbody>
    </table>

    <br>
    <h3> All Assigned Reservations </h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Assigned Waitstaff</th>
                <th>Unassign</th>
            </tr>
        </thead>
        <tbody>
        <form method="post">
            <?php foreach ($all_assigned_reservations as $reservation) {
                $customer = User::getUserById($reservation->useID());
                $waiter =  User::getUserById($reservation->waiterID());
                $fullName = $customer->firstName()." ".$customer->lastName();
                //if statement use one in gerneal to check if at least one is completed
                //do same in waitstaff
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
                    <td><?=  $waiter->firstName()." ".$waiter->lastName()  ?></td>
                    <td><button type="submit" name="unassign" value="<?=  $reservation->resID()  ?>">Unassign</button></td>

                </tr>
            <?php }} ?>
        </form>    
        </tbody>
    </table>



<!-- Logout Button -->
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>



<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
