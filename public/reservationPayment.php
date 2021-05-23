<?php

/**
 * reservationPayment.php
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

// Back
if (array_key_exists('back', $_POST)) {
  $user->setReservationSize(null);
  $user->setReservationTime(null);
  $user->setReservationBalance(null);
  header('Location: ' . $linker->urlPath() . 'customerIndex.php');
}

//Logout 
if (array_key_exists('logout', $_POST)) {
  Session::getSession()->destroy();
  $user = null;
  header('Location: ' . $linker->urlPath() . 'login.php');
}

// Update reservation if it exists, create otherwise, then return to index
if (array_key_exists('pay', $_POST)) {

  $reservation = Reservation::reservationExistenceByUserID($user->id());
  if ($reservation == null) {
    $reservation = new Reservation;
    $reservation->newReservation($user->id(), $user->getReservationTime(), $user->getReservationSize());
    $reservation->addUserToReservation($reservation->resID(), $user->id());
    $reservation->assignReservation(NULL, $reservation->resID());

    $user->setReservationSize(null);
    $user->setReservationTime(null);
    $user->setReservationBalance(null);

    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
  } else {
    $reservation = Reservation::getReservationByUserID($user->id());
    $reservation->setTotalPeople(intval($user->getReservationSize()));
    $reservation->setResTime(intval($user->getReservationTime()));
    $reservation->save();

    $user->setReservationSize(null);
    $user->setReservationTime(null);
    $user->setReservationBalance(null);

    header('Location: ' . $linker->urlPath() . 'customerIndex.php');
  }
}


/////////////////Begin HTML output//////////////////////

//head.html has the needed HTML and styling elements to start every page.
include '../head.html';
?>

<?php
$balance = intval($user->getReservationBalance() * 7); //TODO set reservation price per person as a constant variable
?>
<div class="d-flex justify-content-center" style="padding-bottom: 25px;">
  <h3> Complete and Pay for your Reservation </h3>
</div>
<div class="d-flex justify-content-center" style="padding-bottom: 5px;">
  <h4>Balance: $<?= $balance ?></h4>
</div>
<hr>
<div class="d-flex justify-content-center" style="padding-bottom: 25px;" style="outline: 2px">
  <form class="credit-card" method='post'>
    <div class="form-header">
      <h4 class="title">Payment Information</h4>
    </div>

    <div class="form-body">
      <!-- Card Number -->
      <input type="text" class="card-number" placeholder="Card Number">

      <!-- Date Field -->
      <div class="date-field">
        <div class="month">
          <select name="Month">
            <option value="january">January</option>
            <option value="february">February</option>
            <option value="march">March</option>
            <option value="april">April</option>
            <option value="may">May</option>
            <option value="june">June</option>
            <option value="july">July</option>
            <option value="august">August</option>
            <option value="september">September</option>
            <option value="october">October</option>
            <option value="november">November</option>
            <option value="december">December</option>
          </select>

          <select name="Year">
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
          </select>
        </div>

        <!-- Card Verification Field -->
        <div class="card-verification">
          <div class="cvv-input">
            <input type="text" placeholder="CVV">
          </div>
          <div class="cvv-details">
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="d-flex justify-content-center">
  <form method='post'>
    <input type="submit" name="pay" class="btn btn-success" value="Pay & Finish" style="margin-right: 20px;" />
  </form>

  <form method="post">
    <input type="submit" name="back" class="btn btn-primary" value="Cancel" />
  </form>
</div>






<!-- Navigation Buttons -->
<!--
<form method="post">
    <input type="submit" name="logout" class="button" value="Log Out" />
</form>
-->







<?php

//footer.html has needed HTML and Javascript elements to end every page.
include '../footer.html';
