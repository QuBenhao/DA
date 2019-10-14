<?php
try
{
    session_start();
    if(isset($_SESSION['email']))
    {
        echo '<script>window.location="facebook.php";</script>';
    }
    session_destroy();
    session_start();
    
    // within php use mongo:27017 as the mongo server:port, not
    // localhost:27020 that's for accessing from host computer
    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");

    $filter = [];
    $options = [
        'projection' => ['_id' => 0],
        'sort' => [],
    ];

    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $mng->executeQuery('MyDB.Members', $query);
} catch (MongoDB\Driver\Exception\Exception $e) {

    $filename = basename(__FILE__);

    echo "The $filename script has experienced an error.\n";
    echo "It failed with the following exception:\n";
 
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";
}
    foreach ($rows as $row)
    {
        if( $_POST["email"] == $row->email )
        {
            if($_POST["pass"] == $row->password)
            {
                $_SESSION['email'] = $row->email;
                $_SESSION['screenname'] = $row->screenname;
                echo '<script>window.location="facebook.php";</script>';
            }
        }
        else
            continue;
    }
    echo '<script>window.location="logpage.php";</script>';
    exit;
?>
