<!-- Zefiro HTML Body Template -->
<!-- last known update: 2013-02-04-->
<body>
	<div id="page">
		<div id="header">
			<div class="left">
				<h1 id="title"><?php echo CUSTOM_TITLE ?></h1>
				<p id="subtitle"><?php echo CUSTOM_SUBTITLE ?></p>
			</div> <!-- .left -->
			<div class="right">
				<img id="logo" src="<?php echo CUSTOM_LOGO ?>" alt="<?php echo CUSTOM_LOGO_ALT ?>" title="<?php echo CUSTOM_LOGO_TITLE ?>">
			</div> <!-- .right -->
		</div> <!-- #header -->
		<div class="bar">
			<div class="left">
				<span id="breadcrumbs"><?php echo $dbi->getBreadcrumbs_HTML(); ?></span>
			</div> <!-- .left -->
			<div class="right">
				<span id="print"><a href="<?php echo $_SERVER['REQUEST_URI'].'?'.($_SERVER['QUERY_STRING']?$_SERVER['QUERY_STRING'].'&':'') ?>view=print"><?php echo Z_PRINTABLE_PAGE ?></a></span>
				|
				<span id="languages">
				<?php echo Z_LANGUAGE.': ' ?>
				<?php
					$languages_html = array();
					foreach ($zfLanguageList as $language_name => $language_title) {
						$languages_html[] = '<a href="Z_actions.php?action=setLanguage&'.Z_LANGUAGE_VAR.'='.$language_name.'" title="'.$language_title.'">'.$language_name.'</a>';
					}
					echo implode(' ',$languages_html)
				?>
				</span>
			</div> <!-- .right -->
		</div> <!-- .bar -->
		<div id="options">
			<?php
			// search
			if (basename($_SERVER['SCRIPT_NAME']) != 'search.php' && basename($_SERVER['SCRIPT_NAME']) != 'results.php') {
				$dbi->addOption (DBI_SEARCH,'search.php','icon search');
			}
			if (basename($_SERVER['SCRIPT_NAME']) == 'results.php') {
				$dbi->addOption (DBI_MODIFY_SEARCH,'search.php?'.$dbi->getUserVar('querystring'),'icon search');
			}
			// bookmarks
			if (basename($_SERVER['SCRIPT_NAME']) != 'bookmarks.php') {
				if (count($_SESSION[PROJECT]['bookmarks'])>0)
					$dbi->addOption (DBI_BOOKMARKS,'bookmarks.php','icon bookmarks');
				else
					$dbi->addOption (DBI_BOOKMARKS,'bookmarks.php','icon bookmarksEmpty');
			}
			// admin
			if ($dbi->checkUserPermission('edit') && basename($_SERVER['SCRIPT_NAME']) !== 'Z_contents.php') {
				$dbi->addOption (DBI_CONTENTS,'Z_contents.php','icon contents');
			}
			if ($dbi->checkUserPermission('admin') && basename($_SERVER['SCRIPT_NAME']) !== 'Z_admin.php') {
				$dbi->addOption (DBI_ADMIN,'Z_admin.php','icon admin');
			}
			// authentication
			$dbi->addLoginOption();
			$dbi->showOptions();
			?>
		</div> <!-- #options -->
		<div id="body">
			<div id="content">
				<h2><?php $layout->cast('title') ?></h2>
				<?php $layout->cast('content') ?>
			</div> <!-- #content -->
			<div id="sidebar" class="right">
				<?php $layout->cast('sidebar') ?>
			</div> <!-- #sidebar .right -->
		</div> <!-- #body -->
		<div class="bar">
			<div class="left">
				<?php
					echo Z_VERSION;
					if ($dbi->user) echo ' - '.DBI_AUTHENTICATED_AS.' <b>'.$dbi->user['display_name'].'</b>';
				?>
			</div> <!-- .left -->
			<div class="right">
				<a href="contact.php"><?php echo Z_SITE_CONTACT ?></a>
				|
				<a href="notice.php"><?php echo Z_SITE_NOTICE ?></a>
			</div> <!-- .right -->
		</div> <!-- .bar -->
	</div> <!-- #page -->
</body>
