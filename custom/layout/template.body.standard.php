<!-- Zefiro HTML Body Template -->
<!-- last known update: 2014-01-27-->
<body>
	<div id="page">
		<div id="header">
			<div class="left">
				<h1 id="title"><?php echo MY_TITLE ?></h1>
				<p id="subtitle"><?php echo MY_SUBTITLE ?></p>
			</div> <!-- .left -->
			<div class="right">
				<img id="logo" src="<?php echo MY_LOGO ?>" alt="<?php echo MY_LOGO_ALT ?>" title="<?php echo MY_LOGO_TITLE ?>">
			</div> <!-- .right -->
		</div> <!-- #header -->
		<div class="bar">
			<div class="left">
				<span id="breadcrumbs"><?php echo $dbi->getBreadcrumbs_HTML(); ?></span>
			</div> <!-- .left -->
			<div class="right">
				<span id="print"><a href="<?php echo $_SERVER['REQUEST_URI'].'?'.($_SERVER['QUERY_STRING']?$_SERVER['QUERY_STRING'].'&':'') ?>view=print"><?php echo Z_PRINTABLE_PAGE ?></a></span>
				<?php echo Z_SEPARATOR_SYMBOL ?>
				<span id="languages">
				<?php echo Z_LANGUAGE.': ' ?>
				<?php
					$languages_html = array();
					foreach ($GLOBALS['zefiroLanguages'] as $languageName => $languageTitle) {
						$languagesHtml[] = '<a href="z_actions?action=setLanguage&language='.$languageName.'" title="'.$languageTitle.'">'.$languageName.'</a>';
					}
					echo implode(' ',$languagesHtml)
				?>
				</span>
			</div> <!-- .right -->
		</div> <!-- .bar -->
		<div id="options">
			<?php
			// search
			if (!isServerScriptName('search.php') && !isServerScriptName('results.php')) {
				$dbi->addOption (Z_SEARCH,'search','icon search');
			}
			if (isServerScriptName('results.php')) {
				$dbi->addOption (Z_MODIFY_SEARCH,'search?'.$dbi->getUserVar('querystring'),'icon search');
			}
			// bookmarks
			if (basename($_SERVER['SCRIPT_NAME']) != 'bookmarks') {
				if (count($_SESSION[Z_SESSION_NAME]['bookmarks'])>0)
					$dbi->addOption (Z_BOOKMARKS,'bookmarks','icon bookmarks');
				else
					$dbi->addOption (Z_BOOKMARKS,'bookmarks','icon bookmarksEmpty');
			}
			// admin
			if ($dbi->checkUserPermission('edit') && !isServerScriptName('z_menu_contents.php')) {
				$dbi->addOption (Z_CONTENTS,'z_menu_contents','icon contents');
			}
			if ($dbi->checkUserPermission('admin') && !isServerScriptName('z_menu_admin.php')) {
				$dbi->addOption (Z_ADMIN,'z_menu_admin','icon admin');
			}
			// authentication
			$dbi->addLoginOption();
			$dbi->showOptions();
			?>
		</div> <!-- #options -->
		<div id="body">
			<div id="content">
				<h2><?php $this->cast('title') ?></h2>
				<?php $this->cast('content') ?>
			</div> <!-- #content -->
			<div id="sidebar" class="right">
				<?php $this->cast('sidebar') ?>
			</div> <!-- #sidebar .right -->
		</div> <!-- #body -->
		<div class="bar">
			<div class="left">
				<?php
					echo Z_VERSION;
					if ($dbi->user) echo ' - '.Z_AUTHENTICATED_AS.' <b>'.$dbi->user['display_name'].'</b>';
				?>
			</div> <!-- .left -->
			<div class="right">
				<a href="contact"><?php echo Z_SITE_CONTACT ?></a>
				<?php echo Z_SEPARATOR_SYMBOL ?>
				<a href="notice"><?php echo Z_SITE_NOTICE ?></a>
			</div> <!-- .right -->
		</div> <!-- .bar -->
	</div> <!-- #page -->
</body>
