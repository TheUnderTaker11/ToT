<?php

/**
 * viewCustomerOrder.php
 *
 * A page for employees/managers to view a customer's order and add/remove parts of it
 * @author Clay Bellou
 */


namespace ToT;

//use PDO;

require_once('../bootstrap.php');

// determine if there is a session
$user = Session::getSession()->getUser();

if (!$user) {
    header('Location: ' . Linker::urlPath() . 'login.php');
} elseif ($user->isWaiter() || $user->isHost() || $user->isManager()) {

    $action = $_REQUEST[Constants::PARAM_ACTION];

    $order_ID = null;
    if (array_key_exists(Constants::PARAM_ORDER_ID, $_REQUEST)) {
        $order_ID = intval($_REQUEST[Constants::PARAM_ORDER_ID]);
    } else {
        echo "<h1> Was linked to the page in an invalid way. </h1>";
        exit();
    }
    if ($order_ID == null) {
        echo "<h1> Order ID is null, cannot open page correctly. </h1>";
        exit();
    }
    $order = Order::getOrderById($order_ID);
    $ordersUser = Order::getUserFromOrderID($order_ID);
    $fullName = $ordersUser->firstName() . " " . $ordersUser->lastName();
    $reservation_ID = $order->reservationID();

    //Remove or add items from the order before displaying it!
    if ($action == "deleteItem") {
        //This comes internally
        $item_ID = $_REQUEST["id"];
        $order->deleteItem($item_ID);
        header('Location: ' . Linker::urlPath() . 'viewCustomerOrder.php?orderid=' . intval($order_ID) . "&justdeleted=true");
        die();
    } elseif ($action == Constants::ACTION_ADD_MENU_ITEM_TO_ORDER) {
        //This comes from menu.php page
        $reservation_ID = $_REQUEST[Constants::PARAM_RESERVATION_ID];
        $order_ID = $_REQUEST[Constants::PARAM_ORDER_ID];
        $item_ID = $_REQUEST[Constants::PARAM_ITEM_ID];
        $order->addItem($item_ID, $ordersUser->id());
        header('Location: ' . Linker::urlPath() . 'viewCustomerOrder.php?orderid=' . intval($order_ID));
        die();
    }



    include '../head.html';
    include '../header2.html';

    $menuItemsList = Order::getMenuItemsByOrderID($order_ID);
?>
    <h1>Order information for <?= $fullName ?></h1>
    <form method="POST" action="menu.php?<?= Constants::PARAM_RESERVATION_ID ?>=<?= $reservation_ID ?>&<?= Constants::PARAM_ORDER_ID ?>=<?= $order->id() ?>">
        <button type="submit" class="btn btn-sm btn-primary">Add new item to order</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Food Name</th>
                <th>Price</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menuItemsList as $item) {
            ?>
                <tr>
                    <td><?= $item->name() ?></td>
                    <td><?= $item->price() ?></td>
                    <td>
                        <form method="POST" action="viewCustomerOrder.php?id=<?= $item->id() ?>&action=deleteItem&<?= Constants::PARAM_ORDER_ID ?>=<?= $order->id() ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Remove From Order</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php
    include '../footer.html';
} else {
    //They are customers
    echo "<h1> You do not have permission to use this page <h1>";
}
