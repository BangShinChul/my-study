<!-- content -->
<div class="py-5 text-center" style="background-image: url(&quot;https://pingendo.github.io/templates/sections/assets/cover_event.jpg&quot;);">
    <div class="container py-5">
      	<div class="row">
        	<div class="col-md-12">
	          	<h1 class="display-3 mb-4 text-primary">Bangshinchul.com에&nbsp;<br>오신것을 환영합니다</h1>
	          	<p class="lead mb-5">이 사이트는 저의 개인적인 Codeigniter Study Project 입니다.
				<br>간단한 TODO List, 회원관리기능, 게시판기능을 포함한 Web Application 입니다.&nbsp;</p>
	          	<?php if($this->session->userdata('logged_in') == TRUE) : ?>
	          	<div style="background-color: white; border: 1px solid lightgray;">
					<h4>환영합니다, <?php echo $this->session->userdata('user_id') ?>님!</h4>
					<h5>로그인 정보</h5>
					<p>ID : <?php echo $this->session->userdata('user_id') ?></p>
					<p>NAME : <?php echo $this->session->userdata('user_name') ?></p>
					<p>EMAIL : <?php echo $this->session->userdata('user_email') ?></p>
				</div>
	          	<?php else : ?>
	          	<a href="/index.php/account/create_account" class="btn btn-lg mx-1 btn-secondary">get Sign up</a>
	          	<a href="/index.php/auth/" class="btn btn-lg btn-primary mx-1">Login</a>
	          	<?php endif; ?>
        	</div>
      	</div>
    </div>
</div>
<!-- content -->
