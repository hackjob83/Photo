<?php
// during dev and testing
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// for production
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set("log_errors", 1);
// ini_set("error_log", "/path/to/error_log.log");
$ext_array = array('.jpg','.jpeg','.png');

if (isset($_GET['ext'])) {
    if (in_array($_GET['ext'], $ext_array)) {
        $ext = $_GET['ext'];
    } 
}

require_once 'header.php';
?>

        <title><?php echo SITE_TITLE; ?></title>
    </head>
    <body>
        <div class="container">
            
            <div class="content">
                
                <h1 class="center">Thank you for the submission!</h1>
                
                <?php if(!isset($ext)) {
                    echo "<p>Did you try to mess with something?</p>";
                } else {
                    
                ?>
                
                <div class="center">
                    
                    <p>
                        Here are the original and thumbnail sized photos from your upload. Since this is 
                        just a demo, the image names will over-write each other. There is commented code in 
                        the process page to properly and uniquely name the uploaded images.
                    </p>
                    
                    <img src="images/uploads/DEMO_UPLOAD<?php echo $ext;?>" /><br />
                    <img src="images/uploads/thumbs/200-DEMO_UPLOAD<?php echo $ext;?>" />
                </div>
                
                <?php 
                }
                ?>
        
        <?php require_once 'footer.php'; ?>