<?php

/**
 * customerMakeReservation.php
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

//Generate default reservation object
$reservation = new Reservation;

//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}

//Set reservation size
$reservationSizeValue = $user->getReservationSize();
if (array_key_exists('reservationSizeValue', $_POST)) {
    $user->setReservationSize(intval($_POST['reservationSizeValue']));
}

// Back
if (array_key_exists('back', $_POST)) {
    $user->setReservationSize(null);
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
}

// Generates hooks for the specific make reservation button times
$reservationButtonID = [];
for ($i = 1; $i < $reservation->timeBlocks() - ($reservation->averageReservationLength() / $reservation->timeInterval()) + 2; $i++) {
    array_push($reservationButtonID, $i);
}

$maxCapacity = $reservation->maxCapacity();

foreach ($reservationButtonID as $id) {
    if (isset($_POST[$id])) {

        $capacity = $reservation->getCapacity($id)[0];
        if (is_null($capacity)) {
            $capacity = 0;
        }
        if (is_null($reservationSizeValue)) {
            echo "Failed to add reservation: Reservation size must be set";
        } elseif ($reservationSizeValue + intval($capacity) > $maxCapacity) {
            echo "Failed to add reservation: over maximum capacity";
        } else {
            //$reservation->newReservation($user->id(),$id,$reservationSizeValue);  //submit reservation: moved to payment page
            $user->setReservationTime(intval($id));
            $user->setReservationBalance(intval($reservationSizeValue));
            header('Location: ' . $linker->urlPath() . 'reservationPayment.php');
        }
    }
}


// Generates array of colors dependent on capacity, >0.5 green, < 0.2 red, yellow otherwise.

$colors = [];
for ($i = 1; $i < $reservation->timeBlocks() - ($reservation->averageReservationLength() / $reservation->timeInterval()) + 2; $i++) {
    $capacity =  $reservation->getCapacity($i);
    $ratio = ($maxCapacity - intval($capacity[0])) / $maxCapacity;

    if ($ratio > 0.5) {
        array_push($colors, 'green');
    } elseif ($ratio < 0.2) {
        array_push($colors, 'red');
    } else {
        array_push($colors, 'yellow');
    }
}


/////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
?>

<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
    <h3> Make a Reservation </h3>
</div>
<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
    <ul>
        <li>
            <input class="btn btn-primary btn-xs" value=Grey style="background-color:grey">
            times are unavailable to schedule
        </li>
        <li><input class="btn btn-primary btn-xs" value=Green style="background-color:Green"> times have few scheduled reservations and are not likely to be busy </li>
        <li><input class="btn btn-primary btn-xs" value=Yellow style="background-color:Yellow"> times have moderate remaining capacity but can be freely scheduled</li>
        <li><input class="btn btn-primary btn-xs" value=Red style="background-color:Red"> times are near full capacity and may be unavailable depending on your party size </li>
    </ul>
</div>
<hr>
<!-- Reservation size dropdown-->
<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
    <h5> Reservation Size : </h5>
    <form method="post">
        <?php
        $options = [];
        $options[''] = '';
        for ($i = 1; $i < 11; $i++) { //Max reservation size is set to 10 here
            $options[$i] = $i;
        }
        ?>
        <select name="reservationSizeValue" onchange='this.form.submit()'>
            <?php foreach ($options as $key => $label) { ?>
                <option value="<?= $key ?>" <?= (isset($_POST['reservationSizeValue']) && $_POST['reservationSizeValue'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
            <?php } ?>
        </select>
    </form>
</div>

<hr>
<div id="reservationTimeButtons"></div>

<!-- Navigation Buttons -->
<div class="d-flex justify-content-center">
    <form method='post'>

        <form method="post">
            <input type="submit" name="back" class="btn btn-primary" value="Cancel" />
        </form>
</div>
<!--
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>
-->


<!--
// JS to convert time blocks into standard time format
// i.e 1 -> 8:00AM, 2 -> 8:30AM, etc
-->
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



<!-- Generate buttons for the reservation time slots -->
<script>
    var d = new Date();
    var d2 = new Date(d.getFullYear(), d.getMonth(), d.getDate(), startHour, 0, 0, 0) //set to 8am

    var currentTime = <?= $reservation->currentTimeToTimeBlock(); ?>

    //Array of capacity dependent colors
    var colors = <?php echo json_encode($colors); ?>

    // variables to handle display of the buttons based on conditionals
    var inputType = "submit"
    var color = ""
    var name = ""

    var html = "<table class=\"table table-striped table-sm\"> <tbody> <tr>"
    for (var i = 1; i < timeBlocks - (averageReservationLength / timeInterval) + 2; i++) {
        // disables buttons based on the current time, i.e no reservations for past time slots
        if (i < currentTime) {
            color = "grey"
            name = "disabled"
        } else {
            color = colors[i - 1]
            name = i
        }

        var convertedTime = d2.toLocaleTimeString().replace(/\s+/g, '') // remove spaces from date string
        convertedTime = convertedTime.slice(0, convertedTime.length - 5) + convertedTime.slice(convertedTime.length - 2); // formatting to remove seconds from the date string
        html += " <td><form method=\"post\"> <input type=" + inputType + " STYLE=\"background-color:" + color + "\" name=" + name + " class=\"btn btn-primary\" value=" + convertedTime + "> </form></td>"
        d2.setMinutes(d2.getMinutes() + timeInterval)

        if (i % 4 == 0) {
            html += "</tr>"
            html += "<tr>"
        }

    }
    html += "</tr> </tbody></table>"
    document.getElementById("reservationTimeButtons").innerHTML = html;
</script>



<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
