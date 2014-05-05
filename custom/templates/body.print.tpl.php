<?php
$template_breadcrumbs = preg_replace("/<\/?a[^>]*>/e",'',$dbi->getBreadcrumbs_HTML());
$template_content = preg_replace("/<\/?a[^>]*>/e",'',$template_content);
?>
<body class="print">
	<div id="page" cellspacing="0">
		<div id="header">
			<div class="left">
				<h1><?php echo CUSTOM_TITLE ?></h1>
			</div>
			<div class="right">
				<img src="<?php echo CUSTOM_LOGO ?>" alt="<?php echo CUSTOM_LOGO_ALT ?>" title="<?php echo CUSTOM_LOGO_TITLE ?>">
			</div> <!-- .right -->
		</div>
		<div id="body">
			<div id="content">
				<h2><?php $layout->cast('title') ?></h2>
				<?php $layout->cast('content') ?>
			</div>
		</div>
	</div>
</body>
