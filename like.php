<?php
    include "mongodb.php";
    
    $user = $_GET['email'];
    $objectid = new \MongoDB\BSON\ObjectId($_GET['postid']);

    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    $filter = ['_id'=>$objectid];
    $check = [];
    $options = [
    'sort' => [],
    ];
    $doc = [
    'postid' => $objectid,
    'user' => $user,
    ];
    $query2 = new MongoDB\Driver\Query($check ,$options);
    $rows2 = $mng->executeQuery('MyDB.Likes',$query2);
    foreach($rows2 as $row2)
    {
        if($row2->postid == $objectid && $row2->user==$user)
        {
            echo '<html><head><Script Language="JavaScript">alert("Already liked");</Script></head></html>' .
                "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
            return;
        }
    }
    $query = new MongoDB\Driver\Query($filter,$options);
    $rows = $mng->executeQuery('MyDB.Post',$query);

    foreach($rows as $row)
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $mng->executeBulkWrite('MyDB.Likes', $bulk);
        echo '<html><head><Script Language="JavaScript">alert("Like successfully");</Script></head></html>' .
            "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
        return;
    }
?>
