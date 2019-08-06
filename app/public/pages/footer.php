 </div>   
<!-- end content  -->
		
		
<!-- begin footer -->
<footer>
	<div class="grid-container">
		<div class="grid-x grid-padding-x">
			<div class="cell small-12 medium-8">
				<p>
					&copy; <?= SITE_COPYRIGHT_BEGIN ? SITE_COPYRIGHT_BEGIN.'-' : NULL; ?><?= date("Y", time()).' '.SITE_COPYRIGHT; ?>, All rights reserved
				</p>
			</div>
			
			<div class="cell small-12 medium-4 right">
				<a rel="license" href="/privacy">Privacy</a> | 
				<a rel="license" href="/terms">Terms &amp; Conditions</a>
			</div>
			
		</div>
	</div>
</footer>
<!-- end footer -->

<!-- begin debugging -->
<pre>
<?PHP 
    //print_r($_SESSION); 
    //print_r($_COOKIE); 
    //echo sha1( time() );
?>
</pre>
<!-- end debugging -->
