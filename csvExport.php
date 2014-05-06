<?php
    session_start();
    
    function createCsv($filename, $data) {
        
        
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $filename . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $file = fopen('php://output', 'w');                              

        foreach ($data as $row) {
          fputcsv($file, $row);              
        }
        exit(); 
    }
    
    createCsv($_SESSION['file'], $_SESSION['data']);
?>