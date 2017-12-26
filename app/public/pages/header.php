<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->


<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />

<!-- Set the viewport width to device width for mobile -->
<meta name="viewport" content="width=device-width" />
<!-- Set the viewport width to device width for mobile -->
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- For third-generation iPad with high-resolution Retina display: -->
<link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-touch-icon-144x144.png">
<!-- For iPhone with high-resolution Retina display: -->
<link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-touch-icon-114x114.png">
<!-- For first- and second-generation iPad: -->
<link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-touch-icon-72x72.png">
<!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
<link rel="apple-touch-icon" href="/img/favicons/apple-touch-icon.png">
<link rel="icon" href="/img/favicon.ico" type="image/x-icon" />


<meta name="description" content="Stamp album professional is a database driven stamp album page generator that produces print quality album pages that is free to use.  Stamp album professional uses your web browser to generate and download album pages with no software to download.  All that is required is a computer with an internet connection, Adobe Acrobat(R), and a priter!" />
<meta name="keywords" content="philately, stamps, album, album pages, generators, stamp database, stamp collecting" />
<meta name="author" content="Derrick Haggerty" />
<meta name="copyright" content="Derrick Haggerty. Copyright (c) 2013" />

<title><?= $this->title?$this->title:"SAPro Database Admin"; ?></title>

<!-- Included CSS Files (Uncompressed) -->
<link rel="stylesheet" href="/css/foundation.css">
<link rel="stylesheet" href="/css/app.css">
<link rel="stylesheet" href="/css/modal.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


<!-- IE Fix for HTML5 Tags 
<!--[if lt IE 9]>
		<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body onunload="">
	

<?PHP if(!$noNav){ ?>
<header>
	
	
<nav>
	<div class="name">
		<a href="/">Website Name</a>
	</div>
	<ul>
		<li><a href="/someController">Option</a></li>
		<li><a href="/someController">Option</a></li>
		<li><a href="/someController">Option</a></li>
        <li><a href="/someController">Option</a></li>
	</ul>							  
</nav>
	
<!-- begin error bar -->
<?PHP if( $_REQUEST['msg'] ){ ?>
<div class="msg-bar no-margin" id="msg-bar">
	<p>
	<span class="material-icons md-light md-18 link" data-alert-close="msg-bar">close</span> 
	<?= str_replace("+", " ", $_REQUEST['msg'] ); ?>
    </p>
</div>
<?PHP } elseif( $_REQUEST['error'] ){ ?>
<div class="error-bar no-margin" id="error-bar">
	<p>
	<span class="material-icons md-light md-18 link" data-alert-close="error-bar">close</span> 
	<?= str_replace("+", " ", $_REQUEST['error'] ); ?>
     </p>
</div>
<?PHP } ?>
<!-- end error bar -->	

</header>
<?PHP }  // end no nav ?>
<a name="top"></a>
