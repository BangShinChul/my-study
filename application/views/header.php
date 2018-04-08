<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
	<head>
    	<meta charset="utf-8"/>
    	<meta name="apple-mobile-web-app-capable" content="yes" />
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<!--<link rel="stylesheet" href="/static/css/main.css" type="text/css" media="all">-->
    	<!--
    	<link rel="stylesheet" href="/static/css/bootstrap/bootstrap.css" type="text/css" media="all">
    	<link rel="stylesheet" href="/static/css/bootstrap/bootstrap.min.css" type="text/css" media="all">
    	-->
    	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
		<link rel="stylesheet" href="/static/css/theme.css" type="text/css"> 

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  

    </head>
    <body>
<!-- header -->
  	<div class="py-5 bg-dark">
    	<div class="container">
      		<div class="row">
        		<div class="col-md-12">
		          	<ul class="nav nav-pills" id="navigation">
			            <li class="nav-item" id="home">
			              	<a href="/index.php/main/" class="active nav-link"> <i class="fa fa-home fa-home"></i>&nbsp;Home</a>
			            </li>
			            <li class="nav-item" id="todo">
			              	<a class="nav-link" href="/index.php/todo/">TODO</a>
			            </li>
			            <li class="nav-item" id="board">
			              	<a href="/index.php/board/" class="nav-link">게시판</a>
			            </li>
		            	<?php if(@$this->session->userdata('logged_in') == TRUE) : ?>
			            <li class="nav-item" id="mypage">
			              	<a class="nav-link" href="#">my page</a>
			            </li>
			            <li class="nav-item" id="logout">
			              	<a class="nav-link" href="/index.php/auth/get_logout">logout</a>
			            </li>
		            	<?php else : ?>
			            <li class="nav-item" id="sign up">
			              	<a class="nav-link" href="/index.php/auth/" id="login">login</a>
			            </li>
		            	<?php endif; ?>
		          	</ul>
        		</div>
      		</div>
    	</div>
  	</div>
<!-- header -->

