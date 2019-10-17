<?php
    include 'mongodb.php';

        session_start();
        if(isset($_SESSION['email']))
        {
            session_destroy();
            session_start();
        }

        if($_POST['birthday_year']==0 || $_POST['birthday_month'] ==0 || $_POST['birthday_day'] ==0){
            $dob = new MongoDB\BSON\UTCDateTime(date_create($_POST['dateofbirth']));
        }
        else{
            $date = date_create($_POST['birthday_year'] . "-" . $_POST['birthday_month'] . "-" . $_POST['birthday_day']);
            $dob = new MongoDB\BSON\UTCDateTime($date);
        }
    
        $gender = "";
        if($_POST['sex']==0)
            $gender = $_POST['_gender'];
        else if($_POST['sex']==1)
            $gender = "Female";
        else if($_POST['sex']==2)
            $gender = "Male";
    
        $visibility = "";
        if($_POST['visibility']==0)
            $visibility = $_POST['_visibility'];
        else if($_POST['visibility']==1)
            $visibility = "private";
        else if($_POST['visibility']==2)
            $visibility = "friends-only";
        else if($_POST['visibility']==3)
            $visibility = "public";
    
        $doc = [
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'fullname' => $_POST['fullname'],
        'screenname' => $_POST['screenname'],
        'dateofbirth' => $dob,
        'gender' => $gender,
        'status' => "Online",
        'location' => $_POST['address'],
        'visibility' => $visibility,
        ];
        
    var_dump($doc);
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['email'=>$_POST['email']],$doc);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
    $_SESSION['email'] = $_POST['email'];
    if(!$result){
        echo '<html><head><Script Language="JavaScript">alert("Change Info failed");</Script></head></html>' .
             "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
    }
    else
        echo "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";

?>
