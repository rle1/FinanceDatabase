<?php

    if (isset($_POST['quoteInc'])) {
        header('Location: quoteInc.php');
    }
    else if (isset($_POST['quoteDec'])) {
        header('Location: quoteDec.php');
    }
?>