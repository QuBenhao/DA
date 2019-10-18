<?php
    function findEmail($email){
        // within php use mongo:27017 as the mongo server:port, not
        // localhost:27020 that's for accessing from host computer
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        
        $filter = ['email' => $email];
        $options = [
          //  'projection' => ['_id' => 0],
            'sort' => [],
        ];

        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $mng->executeQuery('MyDB.Members', $query);

        foreach($cursor as $document){
            return $document;
        }
    }
    
    function findUsers($screenname){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        $options = [
          //  'projection' => ['_id' => 0],
            'sort' => [],
        ];
        $searchQuery = array( 'screenname' => new MongoDB\BSON\Regex("^$screenname"));
        $query = new MongoDB\Driver\Query($searchQuery,$options);
        $cursor = $mng->executeQuery('MyDB.Members',$query);

        return $cursor;
    }
    
    function register($doc){
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
        return $result;
    }
    
    function addFriend($user_from,$user_to){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        $filter = [];
        $options = [
             'projection' => ['_id' => 0],
             'sort' => [],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.FriendshipRequest', $query);
        foreach($rows as $row)
        {
            if(($row->requester_email==$user_from && $row->recepient_email==$user_to)||($row->requester_email==$user_to && $row->recepient_email==$user_from))
                return false;
        }
        $doc = [
        'requester_email' => $user_from,
        'recepient_email' => $user_to,
        'request_date' => new MongoDB\BSON\UTCDateTime(),
        'request_status' => "pending"
        ];
        
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);
        
        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $mng->executeBulkWrite('MyDB.FriendshipRequest', $bulk);
        return $result;
     }
    
    function getRequest($user_to){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
        $options = [
            'projection' => ['_id' => 0],
            'sort' => [],
        ];
        
        $searchQuery = array( 'recepient_email' => $user_to,
                             'request_status'=> "pending");
        $query = new MongoDB\Driver\Query($searchQuery,$options);
        $rows = $mng->executeQuery('MyDB.FriendshipRequest',$query);
        
        $str = "";
        
        foreach($rows as $row)
        {
            $document = findEmail($row->requester_email);
            $str .= $row->requester_email . " " . $document->status . "<a href=\"accept.php?from=" . urlencode($row->requester_email) . "&to=" . urlencode($user_to) . "\"><button name=\"accept\" type=\"submit\" value=\"1\" style=\"margin-left:10px\">Confirm</button></a>" . "<a href=\"reject.php?from=" . urlencode($row->requester_email) . "&to=" . urlencode($user_to) . "\"><button name=\"reject\" type=\"submit\" value=\"1\" style=\"margin-left:10px\">Delete</button></a>";
        }
        return $str;
    }
    
    function getFriends($user){
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
        $filter = [];
        $options = [
            'projection' => ['_id' => 0],
            'sort' => [],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.FriendshipRequest', $query);
        $array = [];
        foreach ($rows as $row)
        {
            if($row->requester_email == $user){
                if($row->request_status == "accept"){
                    array_push($array,$row->recepient_email);}
            }
            else if($row->recepient_email == $user){
                if($row->request_status == "accept"){
                    array_push($array,$row->requester_email);}
            }
        }
        return $array;
    }
    
    function isFriend($usera,$userb){
        if(in_array($usera,getFriends($userb)))
            return true;
        return false;
    }
        
    function online($user){
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['email'=>$user],['$set' => ['status' => 'Online']]);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
        return $result;
    }
    
    function offline($user){
        $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['email'=>$user],['$set' => ['status' => 'Offline']]);

        $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $result = $manager->executeBulkWrite('MyDB.Members', $bulk);
        return $result;
    }
    
    function getpost($user)
    {
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
        $filter = [];
        $options = [
            'sort' => ['time'=>-1],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.Post', $query);
        if($rows!=null)
        {
            echo "<br>POSTs";
            foreach ($rows as $row)
            {
               if(isFriend($user,$row->email) || $user==$row->email)
               {
                   if(findEmail($row->email)->visibility!="private" || $user==$row->email)
                   {
                       echo "<div style=\"background-color:white;width=600px;height=100px;margin-top:30px;border:solid 1px grey;font-size:20px\">";
                       echo "<div style=\"float:left;color:blue;\">User: " . findEmail($row->email)->screenname . "</div><div style=\"float:left;margin-left:30px;color:blue;\">Date:" . $row->time->toDateTime()->format("Y-m-d H:i:s") . "</div><br>" . $row->content . "<br>";
                       echo "<div style=\"float:right;margin-right:5px\"></div>";
                       echo "<a href=\"comment.php?email=" . urlencode($user) . "&postid=" . $row->_id . "\"><div style=\"float:right;text-decoration: none;\">Comment</div></a>";
                       echo "<div style=\"float:right;margin-right:15px;width:300px;height:23px\"><form method=\"post\" action=\"addcomment.php?email=" . urlencode($user) . "&postid=" . $row->_id . "\"><input type=\"text\" style=\"box-sizing: border-box;width:280px;height:23px\" placeholder=\"Write a comment\" name=\"comment\" value=\"\"></form></div>";
                       echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"like.php?email=" .  urlencode($user) . "&postid=" . $row->_id . "\"><img src=\"like.png\" width=\"30\" height=\"30\"></a></div>";
                       if($user==$row->email)
                       {
                           echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"deletepost.php?email=" .  urlencode($_SESSION['email']) . "&postid=" . $row->_id . "\"><img src=\"delete.png\" width=\"30\" height=\"30\"></a></div>";
                       }
                       echo "</div><div style=\"clear:both\"></div>";
                   }
               }
                
            }
        }
    }
    
    function getUserpost($user_from,$user_to)
    {
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
        $filter = ['email'=>$user_to];
        $options = [
            'sort' => ['time'=>-1],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mng->executeQuery('MyDB.Post', $query);
        $doc = findEmail($user_to);
        if($user_from == $user_to)
        {
            if($rows!=null)
            {
                echo "<br>POSTs";
                foreach ($rows as $row)
                {
                    echo "<div style=\"background-color:white;width=600px;height=100px;margin-top:30px;border:solid 1px grey;font-size:20px\">";
                    echo "<div style=\"float:left;color:blue;\">User: " . findEmail($row->email)->screenname . "</div><div style=\"float:left;margin-left:30px;color:blue;\">Date:" . $row->time->toDateTime()->format("Y-m-d H:i:s") . "</div><br>" . $row->content . "<br>";
                    echo "<div style=\"float:right;margin-right:5px\"></div>";
                    echo "<a href=\"comment.php?email=" . urlencode($user_from) . "&postid=" . $row->_id . "\"><div style=\"float:right;text-decoration: none;\">Comment</div></a>";
                    echo "<div style=\"float:right;margin-right:15px;width:300px;height:23px\"><form method=\"post\" action=\"addcomment.php?email=" . urlencode($user_from) . "&postid=" . $row->_id . "\"><input type=\"text\" style=\"box-sizing: border-box;width:280px;height:23px\" placeholder=\"Write a comment\" name=\"comment\" value=\"\"></form></div>";
                    echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"like.php?email=" .  urlencode($user_from) . "&postid=" . $row->_id . "\"><img src=\"like.png\" width=\"30\" height=\"30\"></a></div>";
                    echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"deletepost.php?email=" .  urlencode($user_from) . "&postid=" . $row->_id . "\"><img src=\"delete.png\" width=\"30\" height=\"30\"></a></div>";
                    echo "</div><div style=\"clear:both\"></div>";
                }
                    
            }
        }
        else{
            if((isFriend($user_from,$user_to) && $doc->visibility=="friends-only" )||$doc->visibility=="public")
            {
                foreach($rows as $row)
                {
                    echo "<div style=\"background-color:white;width=600px;height=100px;margin-top:30px;border:solid 1px grey;font-size:20px\">";
                    echo "<div style=\"float:left;color:blue;\">User: " . $doc->screenname . "</div><div style=\"float:left;margin-left:30px;color:blue;\">Date:" . $row->time->toDateTime()->format("Y-m-d H:i:s") . "</div><br>" . $row->content . "<br>";
                    echo "<div style=\"float:right;margin-right:5px\"></div>";
                    echo "<a href=\"comment.php?postid=" . $row->_id . "\"><div style=\"float:right;text-decoration: none;\">Comment</div></a>";
                    echo "<div style=\"float:right;margin-right:15px;width:300px;height:23px\"><form method=\"post\" action=\"addcomment.php?email=" . urlencode($user_from) . "&postid=" . $row->_id . "\"><input type=\"text\" style=\"box-sizing: border-box;width:280px;height:23px\" placeholder=\"Write a comment\" name=\"comment\" value=\"\"></form></div>";
                    echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"like.php?email=" .  urlencode($user_from) . "&postid=" . $row->_id . "\"><img src=\"like.png\" width=\"30\" height=\"30\"></a></div></div>";
                    echo "<div style=\"clear:both\"></div>";
                }
            }
        }
    }
    
    function getcomment($postid)
    {
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
        $filter = ['postid'=>$postid];
        $options = [
            'sort' => ['time'=>-1],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        //comment
        $rows = $mng->executeQuery('MyDB.Comments', $query);
        
        $search = ['_id'=>$postid];
        $soptions =['sort' => []];
        $squery = new MongoDB\Driver\Query($search, $soptions);
        //post
        $srows = $mng->executeQuery('MyDB.Post', $squery);
        //comment comment

        
        if($srows!=null)
        {
            foreach($srows as $srow){
                $temp = findEmail($srow->email);
                echo "<div style=\"background-color:white;width=600px;height=100px;margin-top:30px;border:solid 1px grey;font-size:20px\">";
                echo "<div style=\"float:left;color:blue;\">User: " . $temp->screenname . "</div><div style=\"float:left;margin-left:30px;color:blue;\">Date:" . $srow->time->toDateTime()->format("Y-m-d H:i:s") . "</div><br>" . $srow->content . "<br>";
                echo "<div style=\"float:right;margin-right:5px\"></div>";
                echo "<div style=\"float:right;margin-right:15px;width:300px;height:23px\"><form method=\"post\" action=\"addcomment.php?email=" . urlencode($_SESSION['email']) . "&postid=" . $srow->_id . "\"><input type=\"text\" style=\"box-sizing: border-box;width:280px;height:23px\" placeholder=\"Write a comment\" name=\"comment\" value=\"\"></form></div>";
                echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"like.php?email=" .  urlencode($_SESSION['email']) . "&postid=" . $srow->_id . "\"><img src=\"like.png\" width=\"30\" height=\"30\"></a></div></div>";
                echo "<div style=\"clear:both\"></div>";
                break;
            }
        }
        if($rows!=null)
        {
            foreach ($rows as $row)
            {
                $s = ['postid'=>$row->_id];
                $cquery = new MongoDB\Driver\Query($s, $soptions);
                $crows = $mng->executeQuery('MyDB.Comments', $cquery);
                if($crows!=null)
                    echo "<br>Comments<br>";
                echo "<div style=\"background-color:white;width=600px;height=50px;margin-top:30px;border:solid 1px grey;font-size:20px\">";
                echo "User:" . $row->user . ", Time:" . $row->time->toDateTime()->format("Y-m-d H:i:s") . "<br>comments:" . $row->comment . "<br>";
                echo "<div style=\"float:right;margin-right:15px;width:300px;height:23px\"><form method=\"post\" action=\"addcomment.php?email=" . urlencode($_SESSION['email']) . "&postid=" . $row->_id . "\"><input type=\"text\" style=\"box-sizing: border-box;width:280px;height:23px\" placeholder=\"Write a comment\" name=\"comment\" value=\"\"></form></div>";
                echo "<div style=\"float:right;margin-right:15px\"><a role=\"button\" href=\"like.php?email=" .  urlencode($_SESSION['email']) . "&postid=" . $row->_id . "\"><img src=\"like.png\" width=\"30\" height=\"30\"></a></div></div>";
                echo "<div style=\"clear:both\"></div>";
                if($crows!=null)
                {
                    getcomment($row->_id);
                }
            }
        }
    }
    
   function deleteComments($postid)
    {
        $mng = new MongoDB\Driver\Manager("mongodb://mongo:27017");
        $filter = ['postid'=>$postid];
        $options = [
            'sort' => ['time'=>-1],
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        //comment
        $rows = $mng->executeQuery('MyDB.Comments', $query);
        //comment comment
        if($rows!=null)
        {
            foreach ($rows as $row)
            {
                $s = ['postid'=>$row->_id];
                $cquery = new MongoDB\Driver\Query($s, $soptions);
                $crows = $mng->executeQuery('MyDB.Comments', $cquery);
                if($crows!=null)
                {
                    deleteComments($row->_id);
                }
                $manager = new MongoDB\Driver\Manager("mongodb://mongo:27017/MyDB");
                $bulk = new MongoDB\Driver\BulkWrite();
                $bulk->delete($filter);
                $writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
                $result = $manager->executeBulkWrite('MyDB.Comments', $bulk);
            }
        }
    }
?>
