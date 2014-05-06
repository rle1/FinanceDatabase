<?php
    session_start();
    
    $_SESSION['stock'] = $_POST['stock'];
    $_SESSION['startDate'] = $_POST['startDate'];
    $_SESSION['endDate'] = $_POST['endDate'];
    
    if (isset($_POST['compareAll'])) {
        $_SESSION['all'] = true;
    }
    else {
        $_SESSION['all'] = false;
    }
    
    if (isset($_POST['quote'])) {
        header('Location: companyQuote.php');
    }
    else if (isset($_POST['day_hi'])) {
        header('Location: companyDayHi.php');
    }
    else if (isset($_POST['day_lo'])) {
        header('Location: companyDayLo.php');
    }
    else if (isset($_POST['volume'])) {
        header('Location: companyVol.php');
    }
?>