<?php
    include 'mongodb.php';
    try
    {
        session_start();
        if(isset($_SESSION['email']))
        {
            echo '<script>window.location="facebook.php";</script>';
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
    } catch (MongoDB\Driver\Exception\Exception $e) {

        $filename = basename(__FILE__);

        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
     
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
    }
?>
