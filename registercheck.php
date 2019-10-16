<?php
    include 'mongodb.php';

    session_start();
    
    $email = $_POST['email'];
    
    if(isset($_SESSION['email']))
    {
        session_destroy();
        session_start();
    }

    $row = findEmail($email);
    if($row!=null)
    {
        session_destroy();
        echo '<html><head><Script Language="JavaScript">alert("The User already exists!");</Script></head></html>' .
        "<meta http-equiv=\"refresh\" content=\"0;url=logpage.php\">";
    }
    else if(checkEmailformat($email))
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
        
        $result = register($doc);
        
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
    
   function checkEmailformat($email){
        $preg = '/^(\w{1,25})@(\w{1,16})(\.(\w{1,4})){1,3}$/';
        if(preg_match($preg, $email)){
            return true;
        }else{
            echo '<html><head><Script Language="JavaScript">alert("Incorrect Email Format");</Script></head></html>' .
                "<meta http-equiv=\"refresh\" content=\"0;url=register.php\">";
        }
    }
?>
