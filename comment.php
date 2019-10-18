<?php
    include 'mongodb.php';
    session_start();
    if(empty($_SESSION['email']))
    {
        echo '<script>window.location="logpage.php";</script>';
        session_destroy();
    }
    
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Facebook</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
body {
  margin: 0;
  background-color: #eaebef;
}

/* 头部样式 */
.header {
    background-color: #415b97;
    color: White;
    padding-top: 0.1px ;
            box-sizing: border-box;
}

    .a1{
        text-align: left;
        font-size: 40px;
        float: left;
    }
    .search{
        float:left;
        margin-left: 20px;
        margin-top: 20px;
    }
    .a5{
        float:left;
        margin-top: 20px;
        margin-left: 450px;
        width:80px;
        height: 30px;
    }
    .home{
         font-size: 25px;
        text-decoration: none;
    }

    .a6{
        float:right;
        right:0;
        margin-top: 20px;
        margin-right: 100px;
        position: relative;
          display: inline-block;
    }
    .a2{
        float: right;
         margin-top: 20px;
        margin-right: 300px;
      position: relative;
      display: inline-block;
    }
    .friendcontent{
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 350px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      padding: 12px 16px;
      z-index: 1;
      color: black;
    }
    .a2:hover .friendcontent {
      display: block;
    }
    .content{
        height: 28px;
        width:190px;
        float:left;
        margin-left: 0px;
    }
    .butn{
        float: left;
        margin-left: 0px;
    }
    .a3{clear: both;}
     .body{
        width: 100%;
        height: 100%;
        position: absolute;
    }
    .user{
        width:15%;
        height: 40%;
        font-size: 20px;
        margin-left: 15px;
        float:left;
    }
    .userspost{
        margin-left:40px;
        width:60%;
        height: 100%;
        float:left;
        font-size:20px;
    }
     .quitcontent{
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 60px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      padding: 12px 16px;
      z-index: 1;
        color: black;
    }
    .a6:hover .quitcontent {
      display: block;
    }

    .createpost{
        height: 80px;
        width:700px;
    }
    .friend{
        width:20%;
        float:right;
    }
    .postcontent{
        height:2000px;
        width:700px;
        
    }
</style>
<body>
<div class="header">
    <div class="a1">
      <img src="Facebook-2.png" width="50" height="50" alt="">acebook
    </div>
    <div class="search">
        <form method="post" action="#">
            <div class="content">
                <input type="text" name="str" style="width=160px; height: 28px;border:0px;box-sizing: border-box;outline: 1px solid rgba(0, 0, 0, 0); position: relative; width: 100%;z-index: 1;" placeholder="Enter Email address or Screen name" >
            </div>
            <div class="butn">
                <button type="submit" style="height: 28px;position: absolute">
                <img src="/magnifier.png" height=28px>
                </img>
                </button>
            </div>
        </form>
    </div>
    <div class="a5">
        <a class="home" href="facebook.php" style="color:white">
            <m>HOME</m>
        </a>
    </div>
        <div class ="a6">
            <img src="/quit.png" width="40" height="30">
            <div class = "quitcontent">
                <a href=<?php echo "\"logout.php?email=" . urlencode($_SESSION['email']) . "\""?> style="text-decoration: none; color:black"><strong>Log out<br></strong></a>
            </div>
        </div>
            <div class = "a2">
                <img src="friend.png" width="40" height="30" alt="">
                <div class = "friendcontent">
                    <a href="friend.php" style="text-decoration: none; color:black"><strong>Friend requests<br></strong></a>
                    <p><?php
                        if(getrequest($_SESSION['email'])!=null)
                            echo getrequest($_SESSION['email']);
                        else
                            echo "No new request";
                        ?></p>
                </div>
            </div>
    <div class="a3"></div>
</div>
<div class="body">
    <div class="user">
    <p style="margin-top:5px"><strong>Welocome:<br></strong>
    <?php
        $cursor = findEmail($_SESSION['email']);
        echo $cursor->screenname . "<br><br>";
        echo "Birth: " . $cursor->dateofbirth->toDateTime()->format("Y-m-d") . "<br><br>";
        echo "Location: " . $cursor->location . "<br><br>";
        echo "Sex: " . $cursor->gender . "<br><br>";
        echo "Vis: " . strtoupper($cursor->visibility);
    ?>

    </p>
        <form method="POST" action="change.php">
            <input type="hidden" name="email" value=<?php echo $cursor->email;?>>
            <input type="hidden" name="password" value="<?php echo (isset($cursor->password) ? htmlspecialchars($cursor->password) : ''); ?>">
            <input type="hidden" name="fullname" value="<?php echo (isset($cursor->fullname) ? htmlspecialchars($cursor->fullname) : ''); ?>">
            <input type="hidden" name="screenname" value="<?php echo (isset($cursor->screenname) ? htmlspecialchars($cursor->screenname) : ''); ?>">
            <input type="hidden" name="dateofbirth" value=<?php echo  $cursor->dateofbirth->toDateTime()->format("Y-m-d");?>>
            <input type="hidden" name="gender" value=<?php echo $cursor->gender;?>>
            <input type="hidden" name="location" value="<?php echo (isset($cursor->location) ? htmlspecialchars($cursor->location) : ''); ?>">
            <input type="hidden" name="visibility" value=<?php echo $cursor->visibility;?>>
        <button type="submit">Change</button>
        </form>
    </div>
    <div class="userspost">
      <?php
          $postid = new \MongoDB\BSON\ObjectId($_GET['postid']);
          getcomment($postid);
          ?>
    </div>
    <div class="friend">
        <h2>Friends</h2>
        <?php
            $friends = getFriends($_SESSION['email']);
            if($friends!=null){
                foreach($friends as $friend){
                    $frienddocument=findEmail($friend);
                    echo $frienddocument->email . " ";
                    echo $frienddocument->status . "<br>";
                }
            }
            ?>
    </div>
</div>
</body>
</html>






