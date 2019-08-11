<?PHP 
	/*
		This is where your page level logic goes
		
		If the PAGE_PREPROCESS flag is set, then this block will be processed as
		soon as it is added to NerbPage, otherwise it will be processed during 
		render 
	*/
	
	// fetch the database and do something with it...
    //$database = Nerb::fetch('database');
?>

<div class="grid-x grid-margin-x grid-margin-y">
	<div class="cell small-12 medium-6 large-4">
		<div class="splash">
			<img src="/img/nerb_splash.jpg" title="splash"/>
		</div>
	</div>

	<div class="cell small-12 medium-6 large-7 large-offset-1">
		<h2>Congratulations!</h2>
		<h4>The Nerb Application Framework is successfully running.</h4>
		<p>You are seeing this page because you have not yet defined any nodes.</p>
		<p><a href="<?= GIT; ?>" title="See this project on github" target="_blank" class="button action large">
			<i class="material-icons md-32 md-light">arrow_forward</i>&nbsp;Get Started</a>
		</p>
	</div>
	
	<div class="cell small-12 medium-6 large-7 large-offset-5">
		<p>Below are some sample pages to test out the DefaultController</p>
	</div>
	
</div>




