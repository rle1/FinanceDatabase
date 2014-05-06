<?php
    session_start();
    
    $_SESSION['individual'] = $_POST['individual'];
    
    if (isset($_POST['compareAll'])) {
        $_SESSION['all'] = true;
    }
    else {
        $_SESSION['all'] = false;
    }
    
    if (isset($_POST['stocks'])) {
        header('Location: individualHasStocks.php');
    }
    else if (isset($_POST['portfolios'])) {
        header('Location: individualHasPortfolios.php');
    }
    else if (isset($_POST['returns'])) {
        header('Location: individualTotalReturns.php');
    }
    else if (isset($_POST['investments'])) {
        header('Location: individualTotalInvests.php');
    }
    else if (isset($_POST['worth'])) {
        header('Location: individualFinalNetWorth.php');
    }
?>