<?php

/**
 * customerEditReservation.php
 * 
 * Current Code lets the user edit the time/size regardless of the current time. It should stay this way until production to allow easier testing.
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

// Load reservation by userID, if there's an error, return to the main index
$reservation = null;
try {
    $reservation = Reservation::getReservationByUserID($user->id());
} catch (Exception $e) {
    header('Location: ' . $linker->urlPath() . 'login.php');
}

//Logout 
if (array_key_exists('logout', $_POST)) {
    Session::getSession()->destroy();
    $user = null;
    header('Location: ' . $linker->urlPath() . 'login.php');
}

// Back
if (array_key_exists('back', $_POST)) {
    $user->setReservationSize(null);
    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
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


//get reservation size
$reservationSizeValue = $reservation->totalPeople();
if (array_key_exists('reservationSizeValue', $_POST)) {
    $user->setReservationSize(intval($_POST['reservationSizeValue']));
}

// Generates hooks for the specific edit reservation button times
// note that no reservation is allowed if the time between the reservation and closing is less than the average reservation length 
$reservationButtonID = [];
for ($i = 1; $i < $reservation->timeBlocks() - ($reservation->averageReservationLength() / $reservation->timeInterval()) + 2; $i++) {
    array_push($reservationButtonID, $i);
}

$maxCapacity = $reservation->maxCapacity();

foreach ($reservationButtonID as $id) {
    $reservationSizeValueNew = $user->getReservationSize();
    if (isset($_POST[$id])) {
        if (count($_POST) > 1) { //Catches erroneous values when the user updates the reservation size after an fresh log in, the reservation button ids should eventually be changed to unique values to fix this 
            break;
        }
        $capacity = $reservation->getCapacity($id)[0];
        if (is_null($capacity)) {
            $capacity = 0;
        }
        if (is_null($reservationSizeValue)) {
            echo "Failed to edit reservation: Reservation size must be set";
        } elseif ($reservationSizeValueNew + intval($capacity) > $maxCapacity) {
            echo "Failed to edit reservation: over maximum capacity";
        } elseif ($reservationSizeValueNew > $reservationSizeValue) {
            $user->setReservationBalance(intval($reservationSizeValueNew - $reservationSizeValue));
            $user->setReservationSize($reservationSizeValueNew);
            $user->setReservationTime(intval($id));
            header('Location: ' . $linker->urlPath() . 'reservationPayment.php');
        } else {
            $reservation->setResTime(intval($id));
            $reservation->save();
            header('Location: ' . $linker->urlPath() . 'customerIndex.php');
        }
    }
}

if (array_key_exists('reservationSizeUpdate', $_POST)) {
    $reservationSizeValueNew = $user->getReservationSize();
    $capacity = $reservation->getCapacity($id)[0];
    var_dump($reservation);
    if (is_null($capacity)) {
        $capacity = 0;
    }
    if (is_null($reservationSizeValue)) {
        echo "Failed to edit reservation: Reservation size must be set";
    } elseif ($reservationSizeValueNew + intval($capacity) > $maxCapacity) {
        echo "Failed to edit reservation: over maximum capacity";
    } elseif ($reservationSizeValueNew > $reservationSizeValue) {
        $user->setReservationBalance(intval($reservationSizeValueNew - $reservationSizeValue));
        $user->setReservationSize($reservationSizeValueNew);
        $user->setReservationTime($reservation->resTime());

        header('Location: ' . $linker->urlPath() . 'reservationPayment.php');
    } else {
        $reservation->setTotalPeople(intval($reservationSizeValueNew));
        $reservation->save();
        header('Location: ' . $linker->urlPath() . 'customerIndex.php');
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

<!-- Script for generating the correct time format from an existing reservation -->
<?php
if (!is_null($reservation)) {
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
}
?>


<!--Start Display -->


<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
    <h3> Edit your Reservation </h3>
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
<!-- Container for current reservation -->
<div class="d-flex justify-content-center" style="padding-bottom: 25px;" id="viewReservation"></div>
<hr>
<!-- Display logic for reservation container -->
<?php if (is_null($reservation)) {
?>
    <script>
        document.getElementById("viewReservation").innerHTML = "Error: No Reservation Found";
    </script>
<?php
} else {
?>
    <?php $test = $reservation->resTime() ?>
    <script>
        document.getElementById("viewReservation").innerHTML = "Your Current Reservation Time is: ";
        document.getElementById("viewReservation").innerHTML += convertTime(<?php echo $reservation->resTime() ?>)
        document.getElementById("viewReservation").innerHTML += "<br>"
        document.getElementById("viewReservation").innerHTML += "Your Current Reservation Size is: "
        document.getElementById("viewReservation").innerHTML += <?php echo $reservationSizeValue ?>
    </script>
    <div class="d-flex justify-content-center" style="padding-bottom: 25px;">
        <button class="btn btn-primary" id="collapsibleSize" onclick="toggle_visibility()"">Edit Reservation Size</button>
    </div>

    <div id=" editSize" style="padding-bottom: 25px;">
            <h5 class="d-flex justify-content-center"> Select a new Reservation Size:</h5>
            <!-- Reservation size dropdown-->
            <form method="post" class="d-flex justify-content-center">
                <?php
                $options = [];
                $options[''] = '';
                for ($i = 1; $i < 11; $i++) { //max reservation size set to 10
                    $options[$i] = $i;
                }
                if (is_null($reservationSizeValueNew)) { // Logic to set default dropdown value
                ?>
                    <select name="reservationSizeValue" onchange='this.form.submit()'>
                        <?php foreach ($options as $key => $label) { ?>
                            <option value="<?= $key ?>" <?= ($reservationSizeValue == $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php } ?>
                    </select>

                    <form method="post">
                        <input type="submit" class="btn btn-success" name="reservationSizeUpdate" value="Update Only Reservation Size">
                    </form>
                <?php
                } else { ?>
                    <select name="reservationSizeValue" onchange='this.form.submit()'>
                        <?php foreach ($options as $key => $label) { ?>
                            <option value="<?= $key ?>" <?= ($reservationSizeValueNew == $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php } ?>
                    </select>
                    <form method="post">
                        <input type="submit" class="btn btn-success" name="reservationSizeUpdate" value="Update Only Reservation Size">
                    </form>
            </form>
    </div>

<?php } ?>

<hr>
<!-- Container for the reservation buttons -->
<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
    <button class="btn btn-primary" id="collapsibleTimeButtons">Edit Reservation Time</button>


</div>
<div id="reservationTimeButtons" class="d-flex justify-content-center" style="padding-bottom: 25px;"></div>
<br>

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

<!-- Collapsible Container Logic : not currently used-->
<script>
    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
    }
</script>

<?php
} ?>
<!-- End reservation button container-->

<!-- Delete Reservation Button -->
<hr>
<div class="d-flex justify-content-center">
    <form method="post">
        <input type=submit name="delete" value='Delete Reservation' class="btn btn-danger" />
    </form>
</div>

<hr>
<div class="d-flex justify-content-center">
    <!-- Back Button -->
    <form method="post">
        <input type="submit" name="back" value="Cancel" class="btn btn-primary" />
    </form>
</div>


<!-- Logout Button -->
<!--
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>
-->









<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
