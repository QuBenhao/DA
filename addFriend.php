<?php
    include "mongodb.php";
    
    $user_from = $_POST['user_from'];
    $user_to = $_POST['user_to'];
    
    if(addFriend($user_from,$user_to))
        echo "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
    else
    echo '<html><head><Script Language="JavaScript">alert("Add Friend failed");</Script></head></html>' .
        "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
?>
