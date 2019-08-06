<?PHP 
    //$db = Nerb::fetch('database');
    //$CatalogList = new NerbDatabaseTable( $db, 'CatalogList');
    //$Images = new NerbDatabaseTable($db, 'images');
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
		<p><a href="<?= GIT; ?>" title="See this project on github" target="_blank" class="button action large">Get Started</a></p>
	</div>
	
<!--
	
	<div class="cell small-12">
		<h5>Currently Loaded Modules</h5>
		<code>
			<ul>
			<?PHP 
                $modules = Nerb::modules();
                foreach( $modules  as $key => $value ){
                    echo '<li>'.$value.'</li>';
                } // end foreach
            ?>
			</ul>
		</code>
	</div>
	<div class="cell small-12">
		<h5>Current Configuration</h5>
		<code>
			<ul>
			<?PHP 
                $modules = Nerb::status();
                foreach( $modules  as $key => $value ){
                    echo '<li><strong>'.strtolower($key).'</strong> &mdash; '.$value.'</li>';
                } // end foreach
            ?>
			</ul>
		</code>
	</div>
-->
</div>




