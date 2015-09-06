<?php
// during dev and testing
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// for production
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set("log_errors", 1);
// ini_set("error_log", "/path/to/error/log.log");
 
require_once 'includes/config.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        
        <link href="<?php echo BASE_URL; ?>favicon.ico" type="image/x-icon" rel="shortcut icon">
        <link href="<?php echo BASE_URL; ?>favicon.ico" type="image/x-icon" rel="icon">
        
        <meta name="author" content="John Hackett @hackjob83" />
        <meta name="viewport" content="width=device-width" />
        
        <link rel="stylesheet" href="css/normalize.css" type="text/css" />
        <link rel="stylesheet" href="css/style.css" type="text/css" />
        
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        
        
        
        
        