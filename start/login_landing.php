<!DOCTYPE html>
<html>
<head>
		<title>KurshanLab Strain Database</title>
		<meta charset="utf-8">

		<link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/bootstrap/js/bootstrap.min.js"></script>

		<style>
			 body {
				 font-family: "Open Sans";
			 }

			 .form-label-group {
				 padding:10px;
			 }

		</style>

</head>
<body class="bg-light">
	<div class="container">
		<div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" alt="" width="72" height="72">
      <h2>KurshanLab Strain Database</h2>
      <p class="lead">Login</p>
    </div>
    <form class="form-signin needs-validation" action="login.php" method="post">
			<div class="row">
				<div class="col-md-4 mb-3">
					<!-- placeholder -->
				</div>
				<div class="col-md-4 mb-3">

					<!-- <div class="form-label-group"> -->
					<div class="mb-3">
		        <input type="text" id="inputUser" class="form-control" name="username" placeholder="username" required autofocus>
		      </div>

		      <!-- <div class="form-label-group"> -->
					<div class="mb-3">
		        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="password" required>
		      </div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-5 mb-3">
					<!-- placeholder -->
				</div>
				<div class="col-md-2 mb-3 d-flex justify-content-center">
					<input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Login" alt="Submit New User"/>
				</div>
				<div class="col-md-5 mb-3">
					<!-- placeholder -->
				</div>
			</div>
		</form>
	</div>
</body>
</html>
