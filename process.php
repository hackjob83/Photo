<?php
// during dev and testing
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// for production
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set("log_errors", 1);
// ini_set("error_log", "/path/to/error/log.log");

require_once 'includes/config.php';

$_POST = $Gump->sanitize($_POST);

$fname = (isset($_POST['fname']) ? sanitize($_POST['fname']) : '');
$lname = (isset($_POST['lname']) ? sanitize($_POST['lname']) : '');

$fieldReg = '/[a-zA-Z]| |\'|-|\.|\\*/';
$errorExists = false;
$errorString = 'ERROR: The following form fields must be filled out correctly: \n';

if ($fname === '' || strlen($fname) < 2 || strlen($fname) > 50 || preg_match($fieldReg, $fname) === 0) {
    $errorExists = true;
    $errorString .= ' - First Name \n';
}

if ($lname === '' || strlen($lname) < 2 || strlen($lname) > 50 || preg_match($fieldReg, $lname) === 0) {
    $errorExists = true;
    $errorString .= ' - Last Name \n';
}



/* * ****************************************************************************** 
 *                                                                              *
 *                        Photo Upload Validation                               *
 *                                                                              *
 *                                                                              *
 * ****************************************************************************** */

//Possible PHP upload errors 
$file_errors = array(
    1 => 'PHP.ini max file size exceeded',
    2 => 'HTML form max file size exceeded',
    3 => 'File upload was only partial',
    4 => 'No file was attached');

$temp_name = $_FILES["photo"]["tmp_name"];
$orig_name = $_FILES["photo"]["name"];
$field_name = "photo";

$image = array();

if ($_FILES[$field_name]["error"]) {
    $errorExists = true;
    $errorString .= ' - ' . $file_errors[$_FILES[$field_name]["error"]] . '\n';
} else if (!is_uploaded_file($temp_name)) {
    $errorExists = true;
    $errorString .= ' - Not an HTTP upload. \n';
} else if (!$image = getimagesize($temp_name)) {
    $errorExists = true;
    $errorString .= ' - Only image uploads are allowed. \n';
} else if ($_FILES[$field_name]['size'] >= MAX_FILE_SIZE) {
    $uploadErrorExists = true;
    $uploadErrorString .= ' - File can not be larger than 5MB\n';    
} else {

    $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));

    switch ($image['format']) {
        case 'jpg':
            break;
        case 'jpeg':
            break;
        case 'png':
            break;
        case 'gif':
            $errorExists = true;
            $errorString .= ' - Only "jpg", "jpeg", or "png" formats are allowed \n';
            break;
        default:
            $errorExists = true;
            $errorString .= ' - Only "jpg", "jpeg", or "png" formats are allowed \n';
            break;
    }
}

if ($errorExists) {
    include_once('index.php');
    echo '<script type="text/javascript">alert("' . $errorString . '");</script>';
    exit();
} else {

    /*     * *********************************
     *                                       *
     *          PHOTO PROCESSING             *
     *                                       *
     *     ********************************* */

    if ($image['format'] === 'png') {
        $extension = '.' . $image['format'];
    } else {
        $extension = '.jpeg';
    }

    //$new_name = time() . '-' . md5($email);
    $new_name = 'DEMO_UPLOAD';

    $photo = new Photo($_FILES[$field_name]["tmp_name"]);
    $photo->set_orig_name($orig_name);
    $photo->set_new_name($new_name);
    $photo->set_orig_dir("images/uploads/");
    $photo->set_cropped_dir("images/uploads/thumbs/");
    $photo->save_orig();
    $photo->square_crop("thumb");


    // everything should be good and we can do db dump
//    $date = date('Y-m-d');
//    $time = date('H:i:s');
//    $datetime = $date . " " . $time;
//    $ipAddress = $_SERVER['REMOTE_ADDR'];
//    $ua = $_SERVER['HTTP_USER_AGENT'];
//
//    $query = "INSERT INTO smr_users SET";
//    $query .= " fname = :fname";
//    $query .= ", lname = :lname";
//    $query .= ", full_link = :full_link";
//    $query .= ", thumb_link = :thumb_link";
//    $query .= ", date = :date";
//    $query .= ", time = :time";
//    $query .= ", datetime = :datetime";
//    $query .= ", ipAddress = :ip";
//    $query .= ", ua = :ua";
//
//    try {
//        $stmt = $DBH->prepare($query);
//        $stmt->bindValue(':fname', $fname, PDO::PARAM_STR);
//        $stmt->bindValue(':lname', $lname, PDO::PARAM_STR);
//        $stmt->bindValue(':full_link', BASE_URL . 'smr/images/uploads/' . $new_name . $extension, PDO::PARAM_STR);
//        $stmt->bindValue(':thumb_link', BASE_URL . 'smr/images/uploads/thumbs/200-' . $new_name . $extension, PDO::PARAM_STR);
//        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
//        $stmt->bindValue(':time', $time, PDO::PARAM_STR);
//        $stmt->bindValue(':datetime', $datetime, PDO::PARAM_STR);
//        $stmt->bindValue(':ip', $ipAddress, PDO::PARAM_STR);
//        $stmt->bindValue(':ua', $ua, PDO::PARAM_STR);
//        $stmt->execute();
//    } catch (PDOException $ex) {
//        include_once('form.php');
//        echo '<script type="text/javascript">alert("There was an error. Please try again.");</script>';
//        mail(DEVELOPER_EMAIL, 'PDO error CA009', 'error - ' . $ex->getMessage());
//        exit();
//    } catch (Exception $e) {
//        include_once('form.php');
//        echo '<script type="text/javascript">alert("There was an error. Please try again.");</script>';
//        mail(DEVELOPER_EMAIL, 'PDO error CA009', 'error - ' . $e->getMessage());
//        exit();
//    }

    header('Location: thank_you.php?ext='.$extension);
    exit();
}


