<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('service_file');

$form
	->setLabel(NO_LABEL);

$form->addField ('filename',UPLOAD,3000,ALL_FILETYPES,REQUIRED,'images/')
	->setLabel (EJK_FILE);

$form
	->addButton (BACK)
	->addButton (APPLY)
	->addButton (SUBMIT);

$form
	->addAction (REDIRECT,'ejk_contents.php');

if (getUrlParameter('image_filename'))
	$template_title = EJK_EDIT_FILE;
else
	$template_title = EJK_NEW_FILE;

// content
$template_content = '';
$template_content .= $form->run();

// sidebar
$template_sidebar = $dbi->getHelptext_HTML ('upload_images');

// call template
require_once 'templates/ini.php';

?>
