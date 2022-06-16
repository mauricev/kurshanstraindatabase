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
      <p class="lead">Registration</p>
    </div>
    <form class="form-signin needs-validation" action="register.php" oninput='passwordConfirmation.setCustomValidity(passwordConfirmation.value != password.value ? "Passwords do not match." : "")' method="post">
			<div class="row">
				<div class="col-md-4 mb-3">
					<!-- placeholder -->
				</div>
				<div class="col-md-4 mb-3">
					<div class="form-label-group">
		        <input type="text" id="inputUser" class="form-control" name="username" placeholder="pick a username" required autofocus>
		      </div>
					<div class="form-label-group">
		        <input type="email" id="inputEmail" class="form-control" name="email" placeholder="enter email address" required>
		      </div>
		      <div class="form-label-group">
		        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="make up a password" required>
		      </div>
					<div class="form-label-group">
	 				 <input type="password" id="inputPasswordConfirmation" class="form-control" name="passwordConfirmation" placeholder="retype the password" required>
	 			 	</div>
				</div>
			</div>
		<div class="row">
			<div class="col-md-4 mb-3">
				<!-- placeholder -->
			</div>
				<div class="col-md-4 mb-3">
				<input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Submit New User" alt="Submit New User"/>
			</div>
		</div>


      </form>
	</div>
</body>
</html>
