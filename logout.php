<?php
    include "mongodb.php";
    
    if(offline($_GET['email']))
        echo "<meta http-equiv=\"refresh\" content=\"0;url=logpage.php\">";
?>
