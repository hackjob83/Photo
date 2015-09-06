<?php
// during dev and testing
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// for production
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set("log_errors", 1);
// ini_set("error_log", "/path/to/error/log.log");

require_once 'header.php';
?>

<script type="text/javascript" src="js/valid.js"></script>
<script type="text/javascript">
    $(function () {

        // validate registration form
        $('#form').submit(function () {
            var valid = validateForm();
            if (!valid) {
                $('input').blur();
            }
            return valid;
        });
    });
</script>

<title><?php echo SITE_TITLE; ?></title>
</head>
<body>
    <div class="container"> 
        <div id="header"></div>

        <div class="content">
            <div class="form_box">
                <form name="form" id="form" action="process.php" method="post" enctype="multipart/form-data">

                    <p style="text-align: right;">* denotes required fields</p>

                    <label for="fname">* First Name:</label>
                    <input type="text" id="fname" name="fname" value="<?php echo (isset($_POST['fname']) ? sanitize($_POST['fname']) : ''); ?>" maxlength="50" />

                    <label for="lname">* Last Name:</label>
                    <input type="text" id="lname" name="lname" value="<?php echo (isset($_POST['lname']) ? sanitize($_POST['lname']) : ''); ?>" maxlength="50" />

                    <div id="upload_div">
                        <label for="photo">* Photo Upload: </label>
                        <input type="file" id="photo" name="photo" /><br />
                        <small>Please upload a jpg, jpeg, or png image. 5MB size limit</small>
                    </div>


                    <div class="center">
                        <input type="submit" name="submit" id="submit" value="Submit" class="button submit-btn" />
                    </div>


                </form>
            </div>

            <?php
            require_once 'footer.php';
            