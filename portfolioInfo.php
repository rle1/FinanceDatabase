<?php
    session_start();
    
    $_SESSION['portfolio'] = $_POST['portfolio'];
    
    if (isset($_POST['compareAll'])) {
        $_SESSION['all'] = true;
    }
    else {
        $_SESSION['all'] = false;
    }
    
    if (isset($_POST['stocks'])) {
        header('Location: portfolioHasStocks.php');
    }
    else if (isset($_POST['returns'])) {
        header('Location: portfolioTotalReturns.php');
    }
    else if (isset($_POST['investments'])) {
        header('Location: portfolioTotalInvests.php');
    }
    else if (isset($_POST['worth'])) {
        header('Location: portfolioFinalNetWorth.php');
    }
?>