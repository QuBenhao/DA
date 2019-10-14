<?php
    
try{
    session_start();
    
    $email = $_POST['email'];
    
    if(isset($_SESSION['email']))
    {
        session_destroy();
        session_start();
    }
    // within php use mongo:27017 as the mongo server:port, not
    // localhost:27020 that's for accessing from host computer
    
    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
    $db = $mng->MyDB;
    $collection =$db->Members;
    
    $filter = [];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];
    
    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $mng->executeQuery('MyDB.Members', $query);
    
     foreach ($rows as $row)
     {
         if( $email == $row->email )
         {
             session_destroy();
             echo '<html><head><Script Language="JavaScript">alert("The User already exists!");</Script></head></html>' .
             "<meta http-equiv=\"refresh\" content=\"0;url=logpage.php\">";
         }
     }
    
    if(checkEmail($email))
    {
        $fullname = $_POST['fullname'];
        $screenname = $_POST['screenname'];
        $location = $_POST['address'];
        $password = $_POST['password'];
        $date = date_create($_POST['birthday_year'] . "-" . $_POST['birthday_month'] . "-" . $_POST['birthday_day']);
        $dateofbirth = new MongoDB\BSON\UTCDateTime($date);
        $gender = "Unknown";
        if($_POST['sex']==1)
            $gender = "Female";
        else
            $gender = "Male";
        $status = "Offline";
        $visibility = "public";
        
        $doc = [
        'email' => $email,
        'password' => $password,
        'fullname' => $fullname,
        'screenname' => $screenname,
        'dateofbirth' => $dateofbirth,
        'gender' => $gender,
        'status' => $status,
        'location' => $location,
        'visibility' => $visibility,
        ];
        /*
        foreach($doc as $t)
        {
            echo $t . "<br>";
        }
        */
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
        
        if($result == true)
        {
            $_SESSION['email'] = $email;
            $_SESSION['screenname'] = $screenname;
            echo '<html><head><Script Language="JavaScript">alert("Register succeeds");</Script></head></html>' .
                        "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
        }
        else
        {
           echo '<html><head><Script Language="JavaScript">alert("Register fails");</Script></head></html>' .
            "<meta http-equiv=\"refresh\" content=\"0;url=register.php\">";
        }
    }
    
} catch (MongoDB\Driver\Exception\Exception $e) {
    $filename = basename(__FILE__);
    echo "The $filename script has experienced an error.\n";
    echo "It failed with the following exception:\n";
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";
}
    
   function checkEmail($email){
        $preg = '/^(\w{1,25})@(\w{1,16})(\.(\w{1,4})){1,3}$/';
        if(preg_match($preg, $email)){
            return true;
        }else{
            echo '<html><head><Script Language="JavaScript">alert("Incorrect Email Format");</Script></head></html>' .
                "<meta http-equiv=\"refresh\" content=\"0;url=register.php\">";
        }
    }
?>
