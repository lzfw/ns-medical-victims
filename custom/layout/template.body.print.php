<?php
$template_breadcrumbs = preg_replace("/<\/?a[^>]*>/e",'',$dbi->getBreadcrumbs_HTML());
$template_content = preg_replace("/<\/?a[^>]*>/e",'',$template_content);
?>
<body class="print">
	<div id="page" cellspacing="0">
		<div id="header">
			<div class="left">
				<h1><?php echo MY_TITLE ?></h1>
				<p id="subtitle"><?php echo MY_SUBTITLE ?></p>
			</div> <!-- .left -->
			<div class="right">
				<img src="<?php echo MY_LOGO ?>" alt="<?php echo MY_LOGO_ALT ?>" title="<?php echo MY_LOGO_TITLE ?>">
			</div> <!-- .right -->
		</div> <!-- #header -->
		<div id="body">
			<div id="content">
				<h2><?php $this->cast('title') ?></h2>
				<?php $this->cast('content') ?>
			</div>
		</div> <!-- #body -->
	</div> <!-- #page -->
</body>
