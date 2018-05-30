<div id="container">
<header>
	<nav>
		<div class="container">
			<div class="name">
				<a href="/">Logo</a>
			</div>
			<ul>
				<li><a href="#">Option</a></li>
				<li><a href="#">Option</a></li>
				<li><a href="#">Users</a></li>
		        <li><a href="#">Login</a></li>
			</ul>
		</div>
	</nav>	
	
	<!-- begin error bar -->
	<?PHP if( $_REQUEST['msg'] ){ ?>
	<div class="msg-bar no-margin" id="msg-bar">
		<p>
		<span class="material-icons md-light md-18 link" data-alert-close="msg-bar">close</span> 
		<?= urldecode( $_REQUEST['msg'] ); ?>
	    </p>
	</div>
	<?PHP } elseif( $_REQUEST['error'] ){ ?>
	<div class="error-bar no-margin" id="error-bar">
		<p>
		<span class="material-icons md-light md-18 link" data-alert-close="error-bar">close</span> 
		<?= urldecode( $_REQUEST['error'] ); ?>
	     </p>
	</div>
	<?PHP } ?>
	<!-- end error bar -->	



<div class="row">
	<div class="small-12 columns">
		<div class="container">
		<h2 id="header-title">Open Philatelic Foundation</h2>
		<h4>Open source philatelic numbering system</h4>
		</div>
	</div>
</div>

</header>
<a name="top"></a>
