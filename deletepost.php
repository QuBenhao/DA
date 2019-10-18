<?php
    include "mongodb.php";
    
    $user = $_GET['email'];
    $objectid = new \MongoDB\BSON\ObjectId($_GET['postid']);
    
    deleteComments($objectid);

    $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
    $filter = ['_id'=>$objectid];
    $likefilter = ['postid'=>$objectid];
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($filter);
    $likebulk = new MongoDB\Driver\BulkWrite();
    $likebulk->delete($likebulk);

    $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $likeresult = $mng->executeBulkWrite('MyDB.Like',$likebulk);
    if($likeresult)
    {
        $result = $mng->executeBulkWrite('MyDB.Post', $bulk);
        echo '<html><head><Script Language="JavaScript">alert("Delete successfully");</Script></head></html>' .
            "<meta http-equiv=\"refresh\" content=\"0;url=facebook.php\">";
        return;
    }

?>
