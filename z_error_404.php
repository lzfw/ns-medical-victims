<?php
// CMS file: 404 error page (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

// immediately: send error message to admin
require_once 'dbi_error_sendmail.php';

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

// title
$template_title = Z_ERROR_404;

// content
$template_content = $dbi->getHelptext_HTML('error_404');

// sidebar
$template_sidebar = '';

header ('refresh: 10; url=/');

// call template
require_once 'templates/ini.php';

?>
