<?php
    include 'mongodb.php';

        session_start();
        if(isset($_SESSION['email']))
        {
            session_destroy();
            session_start();
        }

        $row = findEmail($_POST['email']);
        if($row!=null)
        {
            if($row->password == $_POST['pass'])
            {
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['screenname'] = $row->screenname;
                echo '<html><head><Script Language="JavaScript">alert("Log in successfully");</Script></head></html>' .
                "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
            }
            else
            {
                echo '<html><head><Script Language="JavaScript">alert("Incorrect password");</Script></head></html>' .
                "<meta http-equiv=\"refresh\" content=\"0;url=logpage.php\">";
            }
        }
        else
        {
            echo '<html><head><Script Language="JavaScript">alert("The user does not exist");</Script></head></html>' .
            "<meta http-equiv=\"refresh\" content=\"0;url=register.php\">";
        }
?>
