<?PHP if( $_REQUEST['msg'] ){ ?>
<!-- begin message bar -->
<div class="msg-bar no-margin" id="msg-bar">
	<p>
	<span class="material-icons md-light md-18 anchor close" data-close="msg-bar">close</span> 
	<?= urldecode( $_REQUEST['msg'] ); ?>
    </p>
</div>
<!-- end message bar -->
<?PHP } elseif( $_REQUEST['error'] ){ ?>
<!-- begin error bar -->
<div class="error-bar no-margin" id="error-bar">
	<p>
	<span class="material-icons md-light md-18 anchor close" data-close="error-bar">close</span> 
	<?= urldecode( $_REQUEST['error'] ); ?>
     </p>
</div>
<!-- end error bar -->	
<?PHP } ?>


<header>
<div class="grid-container">
	<div class="grid-x">
		<div class="cell small-10 medium-4">
			<!--
			<a href="/">
				<img src="/img/logo.png" title="Nerb Application Framework" alt="Nerb Application Framework Logo" class="site-logo"/>
			</a>
			-->
		</div>
	</div>
</div>
</header>

<!-- begin content -->
<div class="grid-container">