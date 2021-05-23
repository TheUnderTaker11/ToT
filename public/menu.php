<?php

/**
 * menu.php
 *
 * @author Clay Bellou
 */


namespace ToT;

use PDO;

require_once('../bootstrap.php');

// determine if there is a session
$user = Session::getSession()->getUser();
$error = null;
$reservation = null;
$order = null;
$reservation_ID = null;
$order_ID = null;

// Back
if (array_key_exists('back', $_POST)) {
    $linker = new Linker;
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
}


$action = @$_REQUEST[Constants::PARAM_ACTION];
if ($action == Constants::ACTION_ADD_MENU_ITEM_TO_ORDER) {
    $item_ID = $_REQUEST[Constants::PARAM_ITEM_ID];
    $order = Order::getOrderByUserId($user->id());
    $order->addItem($item_ID, $user->id());
    header('Location: ' . Linker::urlPath() . 'customerIndex.php');
}

// See if the reservationID and orderID have been included.
//Accept both URL params and POST request.
$action = @$_REQUEST[Constants::PARAM_ACTION];
if (array_key_exists(Constants::PARAM_RESERVATION_ID, $_REQUEST) && array_key_exists(Constants::PARAM_ORDER_ID, $_REQUEST)) {
    $reservation_ID = intval($_REQUEST[Constants::PARAM_RESERVATION_ID]);
    $order_ID = intval($_REQUEST[Constants::PARAM_ORDER_ID]);
} elseif ($action == Constants::ACTION_ADD_MENU_ITEM_TO_ORDER) {
    $reservation_ID = $_POST[Constants::PARAM_RESERVATION_ID];
    $order_ID = $_POST[Constants::PARAM_ORDER_ID];
}
if ($reservation_ID != null && $order_ID != null) {
    if (!$user) {
        header('Location: ' . Linker::urlPath() . 'login.php');
    }
    try {
        $reservation = Reservation::getReservationByReservationID(intval($reservation_ID));
        //This function is broken for some reason, no idea why.
        //$order = Order::getOrderById(intval($orderID));
        $order = Order::getOrderById($order_ID);
        if (!($user->isEmployee()) && $order->userID() != $user->id()) {
            echo '<h1>Invalid permission to add items to this order! Displaying default menu page instead.</h1>';
            $reservation = null;
            $order = null;
            $reservation_ID = null;
            $order_ID = null;
        }
    } catch (Database\Exception $e) {
        // if no user was found display the error and drop out with a dummy action
        echo '<h1>Invalid reservation or order ID passed in, just displaying normal menu page.</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        $reservation = null;
        $order = null;
    }
}

///////////////////// BEGIN MAIN OUTPUT///////////////////////////////
include '../head.html';
include '../header2.html';
echo "<h1>ToT Menu</h1>";
if ($reservation != null && $order != null) {
    echo "<h3>Add whatever item you want to your order, or, just leave the page to cancel this!</h3>";
}
$all_menu_items = MenuItem::getAllMenuItems();

$previous_category = "NULL/NONE";
$firstRun = true;
foreach ($all_menu_items as $item) {
    //Each category will have it's own table.
    //So if the new item has a different category, then we want to make a new table for it.
    if ($item->category() != $previous_category) {
        //Ensure we don't have closing tags without any tables existing in the first place.
        if (!$firstRun) {
            closePreviousTable();
        } else {
            $firstRun = false;
        }
        openNewTable($item->category(), $reservation, $order);
    }

    addNewRow($item, $reservation, $order, $user->isEmployee());
}
//Now all items are done, close the final table.
closePreviousTable();

?>

<div class="d-flex justify-content-center">
    <form method="post">
        <input type="submit" name="back" class="btn btn-primary" value="Back" />
    </form>
</div>
<?php


include '../footer.html';

/**
 * Creates a new table row for the given MenuItem
 * 
 * @param MenuItem
 * @param Reservation nullable
 * @param Order nullable
 */
function addNewRow($item, $reservation, $order, $isEmployee)
{
?>

    <tr>
        <td><img src="<?= $item->image() ?>" alt="Image of a(n) <?= $item->name() ?>" height=100 width=100></img></td>
        <td><?= $item->name() ?></td>
        <td><?= $item->description() ?></td>
        <td><?= $item->price() ?></td>
        <?php if ($reservation != null && $order != null) { ?>
            <td>
                <?php
                if ($isEmployee) {
                ?>
                    <form method="POST" action="viewCustomerOrder.php?<?= Constants::PARAM_ACTION ?>=<?= Constants::ACTION_ADD_MENU_ITEM_TO_ORDER ?>&<?= Constants::PARAM_RESERVATION_ID ?>=<?= $reservation->resID() ?>&<?= Constants::PARAM_ORDER_ID ?>=<?= $order->id() ?>&<?= Constants::PARAM_ITEM_ID ?>=<?= $item->id() ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Add to Order</button>
                    </form>
                <?php
                } else {
                ?>
                    <form method="POST" action="menu.php?<?= Constants::PARAM_ACTION ?>=<?= Constants::ACTION_ADD_MENU_ITEM_TO_ORDER ?>&<?= Constants::PARAM_RESERVATION_ID ?>=<?= $reservation->resID() ?>&<?= Constants::PARAM_ORDER_ID ?>=<?= $order->id() ?>&<?= Constants::PARAM_ITEM_ID ?>=<?= $item->id() ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Add to Order</button>
                    </form>
                <?php
                }
                ?>
            </td>
        <?php } ?>
    </tr>
<?php
}
/**
 * Just closing tags for a mid-creation table.
 */
function closePreviousTable()
{
    echo "</tbody>";
    echo "</table>";
}

/**
 * Opens a new table for the given category.
 * (Ensure you have closed the previous table first though!)
 * 
 * reservation and order can be null, when just viewing the menu to look at all the items.
 * 
 * @param string category, required
 * @param Reservation nullable
 * @param Order nullable
 */
function openNewTable($category, $reservation, $order)
{
?>
    <br />
    <br />
    <h2> <?= $category ?> </h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <?php
                if ($reservation != null && $order != null) {
                ?>
                    <th>Action</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
    }
