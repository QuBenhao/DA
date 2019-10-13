<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Facebook</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
}
	.a1{
		text-align: left;
		font-size: 40px;
		float: left;
	} 
	.home{
		float:left;
		margin-top: 25px;
		margin-left: 750px;
		font-size: 20px;
	}
	.a2{
		float: right;
		margin-top: 20px;
		margin-right: 100px;
	}
	.a3{clear: both;}
	.a4{
		background-color: #415b97;
	}
</style>
</head>
<body>
<div class="header">
	
    <div class="a1"> 
	  <img src="Facebook-2.png" width="50" height="50" alt="">acebook
	</div>
	<a class="home" href="/facebook.php" role="button">
	<div class="home1">
		<m>HOME</m>
	</div>
	</a>
	<a class="a4" href="/friend.html" role="button" name="requests">
	<div class = "a2">	
		<img src="friend.png" width="40" height="30" alt="">
	</div>
	</a>
	<div class="a3"></div>
	
  <form method="post" action="save.php"></form>
</div>
    <p><strong>Welocome:<br><?php echo $_POST["email"]; ?></strong></p>
</body>
</html>
