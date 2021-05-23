<?php

/**
 * customerIndex.php
 */

namespace ToT;

use Exception;

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

//Load reservation and order by userID
$reservation = null;
$order = null;
try {
    $reservation = Reservation::getReservationByUserID($user->id());
} catch (Exception $e) {
}

try {
    $order = Order::getOrderByUserId($user->id());
} catch (Exception $e) {
}

//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}

//Delete Reservation 
if (array_key_exists('delete', $_POST)) {
    delete($user->id());
}
function delete($userID)
{
    $reservation = Reservation::getReservationByUserID($userID);
    if ($reservation->deleteThreshold()) {
        $reservation->deleteReservationByUserID($userID);
        header('Location: ' . $_SERVER['REQUEST_URI']);
    } else {
        echo "Unable to delete reservation: within 2 hour window";
    }
}

// Reservation navigation
if (array_key_exists('makeReservation', $_POST)) {
    header('Location: ' . $linker->urlPath() . 'customerMakeReservation.php');
}

if (array_key_exists('assignReservation', $_POST)) {
    $resID = $_POST['assignReservation'];
    try {
        if (!is_null(Reservation::getReservationByReservationID($resID))) {
            Reservation::addUserToReservation($resID, $user->id());
        }
    } catch (Exception $e) {
        echo "Reservation ID does not exist";
    }
}

if (array_key_exists('editReservation', $_POST)) {
    header('Location: ' . $linker->urlPath() . 'customerEditReservation.php');
}

if (array_key_exists('payParty', $_POST)) {
    $user->setOrderBalance($reservation->getTotalReservationCost()[0]);
    $user->setPaymentType("party");
    header('Location: ' . $linker->urlPath() . 'orderPayment.php');
}

if (array_key_exists('paySelf', $_POST)) {
    $user->setOrderBalance($order->cost());
    $user->setPaymentType("self");
    header('Location: ' . $linker->urlPath() . 'orderPayment.php');
}

$action = @$_REQUEST[Constants::PARAM_ACTION];

// Delete item from order
if ($action == 'deleteItem') {
    $item_ID = $item_ID = $_REQUEST['item'];
    $order->deleteItemByOrderMenuItemsID($item_ID);
}

//Add item to order: Moved to menu.php
/*
if ($action == Constants::ACTION_ADD_MENU_ITEM_TO_ORDER) {
    $item_ID = $_REQUEST[Constants::PARAM_ITEM_ID];
    var_dump($_REQUEST);
    $order->addItem($item_ID, $user->id());
}
*/
/////////////////Begin HTML output//////////////////////


//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
include '../header2.html';
?>



<!-- Script for generating the correct time format from an existing reservation -->
<?php
if (!is_null($reservation)) { // If user has created a reservation
?>
    <script>
        var startHour = <?= $reservation->startHour() ?>; //8 am
        var timeBlocks = <?= $reservation->timeBlocks() ?>; // 8am to 8 pm
        var timeInterval = <?= $reservation->timeInterval() ?>; // in minutes
        var averageReservationLength = <?= $reservation->averageReservationLength() ?>; // in minutes

        function convertTime(resTime) {

            var d = new Date();
            var d2 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), startHour, 0, 0, 0) //set to 8am

            d2.setMinutes(d2.getMinutes() + (resTime - 1) * timeInterval)
            var convertedTime = d2.toLocaleTimeString().replace(/\s+/g, '') // remove spaces from date string
            convertedTime = convertedTime.slice(0, convertedTime.length - 5) + convertedTime.slice(convertedTime.length - 2); // formatting to remove seconds from the date string
            return (convertedTime)
        }
    </script>

<?php
} elseif (!is_null($user->getReservationID())) { // Grabs temp reservation object associated with userID then removes it for the conditional logic below
    $reservation = Reservation::getReservationByReservationID($user->getReservationID()[0]);
?>

    <script>
        var startHour = <?= $reservation->startHour() ?>; //8 am
        var timeBlocks = <?= $reservation->timeBlocks() ?>; // 8am to 8 pm
        var timeInterval = <?= $reservation->timeInterval() ?>; // in minutes
        var averageReservationLength = <?= $reservation->averageReservationLength() ?>; // in minutes

        function convertTime(resTime) {

            var d = new Date();
            var d2 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), startHour, 0, 0, 0) //set to 8am

            d2.setMinutes(d2.getMinutes() + (resTime - 1) * timeInterval)
            var convertedTime = d2.toLocaleTimeString().replace(/\s+/g, '') // remove spaces from date string
            convertedTime = convertedTime.slice(0, convertedTime.length - 5) + convertedTime.slice(convertedTime.length - 2); // formatting to remove seconds from the date string
            return (convertedTime)
        }
    </script>

<?php
    $reservation = null;
} ?>

<!--Start Display -->

<h2> Hello
    <?= $user->firstName() . " " . $user->lastName() ?>
</h2>
<hr>
</br>

<!-- Display logic for reservation container -->
<?php if (is_null($reservation) && is_null($order) && is_null($user->getReservationID())) { // No reservation or order associated with user
?>
    <div id="viewReservation">
        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            You currently do not have a reservation, would you like to make one?
            </br>
        </div>

        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            </br>
            <form method="post">
                <input class="btn btn-success" type=submit name="makeReservation" value='Make a Reservation'>
            </form>
        </div>

        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            If you have been given a Reservation Number, you can input it here to get your Reservation:
            </br>
        </div>

        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            </br>
            <form action="" method="post">
                <input type="number" name="assignReservation" placeholder="Reservation Number" />
                <input class="btn btn-success" name="form" type="submit" value="Submit" />
            </form>
        </div>
        <hr>

        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            <form method="POST" action="menu.php">
                <input class="btn btn-primary" type="submit" class="button" value="View the Menu" />
            </form>
        </div>
    </div>

<?php
} elseif (is_null($order) && !is_null($reservation)) { // User is the creator of a reservation, but host has not assigned an order
?>
    <div id="viewReservation">
        <div id="reservationText" class="d-flex justify-content-center" style="padding-bottom: 5px;">
        </div>
        <div id="reservationIDText" class="d-flex justify-content-center" style="padding-bottom: 50px;">
        </div>
        <!-- Reservation manipulation buttons -->
        <div class="d-flex justify-content-center" style="padding-bottom: 5px;">
            <form method="post">
                <input class="btn btn-success" type=submit name="editReservation" value='Edit Reservation' style="margin-right: 25px;">
            </form>
            <form method="post">
                <input class="btn btn-danger" type=submit name="delete" value='Delete Reservation'>
            </form>
        </div>
        <div id="reservationEditText" class="d-flex justify-content-center" style="padding-bottom: 25px;">
        </div>
        <hr>
        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            <form method="POST" action="menu.php">
                <input class="btn btn-primary" type="submit" class="button" value="View the Menu" />
            </form>
        </div>

    </div>

    <script>
        // Reservation status text
        var time = convertTime(<?php echo $reservation->resTime() ?>);
        document.getElementById("reservationText").innerHTML += "You have a reservation scheduled at ";
        document.getElementById("reservationText").innerHTML += time;
        document.getElementById("reservationText").innerHTML += " for ";
        document.getElementById("reservationText").innerHTML += <?= $reservation->totalPeople() ?>;
        if (<?= $reservation->totalPeople() ?> > 1) {
            document.getElementById("reservationText").innerHTML += " people.";
        } else {
            document.getElementById("reservationText").innerHTML += " person.";
        }

        // Reservation ID text
        document.getElementById("reservationIDText").innerHTML += "Your reservation number is: ";
        document.getElementById("reservationIDText").innerHTML += <?= $reservation->resID() ?>;

        // Reservation edit text
        document.getElementById("reservationEditText").innerHTML += "You can edit or delete your reservation until ";
        var time = convertTime(<?php echo ($reservation->resTime() - 4) ?>); // 2 hours before reservation time
        document.getElementById("reservationEditText").innerHTML += time;
    </script>

<?php // User is associated with a reservation but not the creator of the reservation, and has no order assigned
} elseif (!is_null($user->getReservationID()) && is_null($order)) {
    $reservation = Reservation::getReservationByReservationID($user->getReservationID()[0]);
?>
    <div id="viewReservation">
        <div id="reservationText" class="d-flex justify-content-center" style="padding-bottom: 5px;">
        </div>
        <div id="reservationIDText" class="d-flex justify-content-center" style="padding-bottom: 50px;">
        </div>
        <hr>
        <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
            <form method="POST" action="menu.php">
                <input class="btn btn-primary" type="submit" class="button" value="View the Menu" />
            </form>
        </div>

    </div>

    <script>
        // Reservation status text
        var time = convertTime(<?php echo $reservation->resTime() ?>);
        document.getElementById("reservationText").innerHTML += "You have a reservation scheduled at ";
        document.getElementById("reservationText").innerHTML += time;

        // Reservation ID text
        document.getElementById("reservationIDText").innerHTML += "Your reservation number is: ";
        document.getElementById("reservationIDText").innerHTML += <?= $reservation->resID() ?>;
    </script>
<?php // User is assigned an order, order is not complete
} elseif ($order->complete() == 0) { ?>

    <!-- Container for current order -->
    <div id="orderText" class="d-flex justify-content-center" style="padding-bottom: 25px;">
        <h3>My Order</h3>
    </div>

    <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
        <form method="POST" action="menu.php?<?= Constants::PARAM_RESERVATION_ID ?>=<?= $order->reservationID() ?>&<?= Constants::PARAM_ORDER_ID ?>=<?= $order->id() ?>">
            <input type="submit" value="Add Item / View Menu" class="btn btn-primary" />
        </form>
    </div>
    <div id="orderTable">

        <?php
        $items = $order->getMenuItemsByOrderIDAndUserID($order->id(), $user->id());
        $orderItems = $order->getOrderItems();
        $count = 0;
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) {
                ?>
                    <tr>
                        <td><?= $item->name() ?></td>
                        <td><?= $item->category() ?></td>
                        <td><?= $item->description() ?></td>
                        <td><?= $item->price() ?></td>
                        <td>
                            <form method="POST" action="customerIndex.php?<?= Constants::PARAM_ACTION ?>=deleteItem&item=<?= $orderItems[$count][0] ?>">
                                <input type="submit" class="btn btn-danger" value="Delete" />
                            </form>
                        </td>
                    </tr>
                <?php
                    $count += 1;
                } ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        <h4 style="margin-right: 50px">My Total: <?= $order->cost() ?></h4>
        <?php if (!is_null($reservation)) {
        ?>
            <div id="partyTotal">
                <h4>Party Total: <?= $reservation->getTotalReservationCost()[0] ?></h4>
            </div>
            <script>
                if (<?= $reservation->useID() ?> != <?= $user->id() ?>) {
                    document.getElementById("partyTotal").innerHTML = "";
                }
            </script>
        <?php } ?>
    </div>



    <div class="d-flex justify-content-center">
        <?php
        if ($order->cost() <= 0) { // Temp fix to erroneous order amounts from being paid
            $name = "disabled";
        } else {
            $name = 'paySelf';
        } ?>
        <form method="POST">
            <input type="submit" name=<?= $name ?> value="Pay For My Order" class="btn btn-success" style="margin-right: 50px" />
        </form>

        <?php
        if (!is_null($reservation)) {
            if ($reservation->getTotalReservationCost()[0] <= 0) { // Temp fix to erroneous order amounts from being paid
                $name = "disabled";
            } else {
                $name = 'payParty';
            } ?>

            <form method="POST" id="payPartyForm">
                <input type="submit" name=<?= $name ?> value="Pay for My Party" class="btn btn-success" />
            </form>
            <script>
                if (<?= $reservation->useID() ?> != <?= $user->id() ?>) {
                    document.getElementById("payPartyForm").remove()
                }
            </script>

        <?php } ?>
    </div>


<?php // User is assigned an order, and the order is complete
} else { ?>

    <!-- Container for current order -->
    <div id="orderText" class="d-flex justify-content-center" style="padding-bottom: 25px;">
        <h3>Your order is paid for and completed. Contact your server to begin a new order.</h3>
    </div>

    <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
        <form method="POST" action="menu.php">
            <input class="btn btn-primary" type="submit" class="button" value="View the Menu" />
        </form>
    </div>
    <div id="orderTable">

        <?php
        $items = $order->getMenuItemsByOrderIDAndUserID($order->id(), $user->id());
        $orderItems = $order->getOrderItems();
        $count = 0;
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) {
                ?>
                    <tr>
                        <td><?= $item->name() ?></td>
                        <td><?= $item->category() ?></td>
                        <td><?= $item->description() ?></td>
                        <td><?= $item->price() ?></td>
                    </tr>
                <?php
                    $count += 1;
                } ?>
            </tbody>
        </table>
    </div>
    <div>
        <h4>My Total: <?= $order->cost() ?></h4>
    </div>
<?php
} ?>

<!-- Logout Button -->
<!--
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>
-->








<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
