<?php

// TRANSLATION FILE: GERMAN
// last known update: 2014-01-27

setlocale (LC_ALL,'de_DE@euro','de_DE','de','ge');

// ZEFIRO FRAMEWORK -----------------------------------------------------------

define('Z_ERROR_404',					'Fehler 404');
define('Z_LANGUAGE',					'Sprache');
define('Z_SITE_CONTACT',				'Kontakt');
define('Z_SITE_NOTICE',					'Impressum');
define('Z_PRINT',						'Drucken');
define('Z_PRINTABLE_PAGE',				'Druckansicht');
define('Z_VERSION',						'Zefiro 4.0.0');

// general phrases ------------------------------------------------------------

define('Z_HOME',						'Startseite');
define('Z_CONTACT',						'Kontakt');
define('Z_EMAIL',						'E-Mail');
define('Z_COMMENT',						'Kommentar');
define('Z_FORMAT',						'Format');

define('Z_SEARCH',						'Suche');
define('Z_QUICK_SEARCH',				'Schnellsuche');
define('Z_ADVANCED_SEARCH',				'Erweiterte Suche');
define('Z_MODIFY_SEARCH',				'Suche verändern');
define('Z_NEW_SEARCH',					'Neue Suche');

// list browser ---------------------------------------------------------------

define('Z_RESULTS_NEXT',				'nächste ');
define('Z_RESULTS_PREVIOUS',			'vorige ');
define('Z_RESULTS_FIRST',				'Anfang');
define('Z_RESULTS_LAST',				'Ende');
define('Z_OF_TOTAL_RESULTS',			'von');
define('Z_RESULTS_ORDER',				'Reihenfolge');
define('Z_RESULTS',						'Ergebnisse');
define('Z_NO_RESULTS',					'Die Anfrage ergab keine Ergebnisse.');
define('Z_ERROR_QUERY',					'Die Anfrage konnte nicht ausgeführt werden.');

// browsing -------------------------------------------------------------------

define('Z_FORWARD',						'vor');
define('Z_BACK',						'zurück');

// bookmarks ------------------------------------------------------------------

define('Z_BOOKMARK',					'Lesezeichen');
define('Z_BOOKMARKS',					'Lesezeichen');
define('Z_BOOKMARKS_EMPTY',				'Momentan sind keine Lesezeichen auf der Liste.');
define('Z_BOOKMARKS_DUMPED',			'Alle Lesezeichen wurden entfernt.');
define('Z_VIEW_BOOKMARKS',				'Lesezeichen öffnen');
define('Z_ADD_BOOKMARK',				'Lesezeichen hinzufügen');
define('Z_REMOVE_BOOKMARK',				'Lesezeichen entfernen');
define('Z_SEND_BOOKMARKS',				'Lesezeichen versenden');
define('Z_DUMP_BOOKMARKS',				'Alle Lesezeichen entfernen');
define('Z_BOOKMARKS_ASK_EMAIL',			'Bitte geben Sie die E-Mail-Adresse des Empfängers ein.');

// user authentication --------------------------------------------------------

define('Z_LOGIN',						'Login');
define('Z_LOGOUT',						'Logout');
define('Z_LOGIN_PROMPT',				'Bitte geben Sie Ihren Benutzernamen und Ihr Passwort ein.');
define('Z_INVALID_LOGIN',				'Ungültiger Login. Bitte überprüfen Sie die Angaben.');
define('Z_LOGGED_OUT',					'Ihre Sitzung wurde beendet.');
define('Z_AUTHENTICATED_AS',			'angemeldet als');

// user permissions -----------------------------------------------------------

define('Z_PERMISSION_REQUIRED',			'Berechtigung erforderlich');
define('Z_PERMISSIONS_INSUFFICIENT',	'Sie haben keine ausreichende Berechtigung, diese Seite aufzurufen.');
define('Z_LOGIN_OR_GO_HOME',			'Bitte loggen Sie sich ein oder kehren Sie zur Startseite zurück.');
define('Z_RELOGIN_OR_GO_HOME',			'Bitte loggen Sie sich mit einem anderen Account ein, welches über diese Berechtigungen verfügt, oder kehren Sie zur Startseite zurück.');

// admin tools ----------------------------------------------------------------

define('Z_ADMIN',						'Systemsteuerung');
define('Z_DATABASE',					'Datenbank');
define('Z_DATABASE_STATUS',				'Status');
define('Z_DATABASE_MAINTENANCE',		'Wartungsaufgaben');
define('Z_DATABASE_OPTIMIZE',			'Optimieren');
define('Z_DATABASE_NO_TASKS',			'Es stehen keine Wartungsaufgaben an.');
define('Z_DATABASE_IMPORT',				'Import');
define('Z_DATABASE_IMPORT_OK',			'Der Import der Daten ist abgeschlossen.');
define('Z_DATABASE_BACKUP',				'Sicherung');
define('Z_DATABASE_BACKUP_OK',			'Eine Sicherung der Daten wurde erstellt.');
define('Z_DATABASE_RECOVERY',			'Wiederherstellung');
define('Z_DATABASE_RECOVERY_OK',		'Die Wiederherstellung der Daten ist abgeschlossen.');

// user accounts --------------------------------------------------------------

define('Z_USERS',						'Nutzer');
define('Z_USER_ACCOUNT',				'Nutzerkonto');
define('Z_USER_ACCOUNTS',				'Nutzerkonten');
define('Z_NEW_USER_ACCOUNT',			'Neues Nutzerkonto');
define('Z_EDIT_USER_ACCOUNT',			'Nutzerkonto bearbeiten');
define('Z_REMOVE_USER_ACCOUNT',			'Nutzerkonto entfernen');

define('Z_REMOTE_ACCESS',				'Remotezugriff');
define('Z_REMOTE_ACCESSES',				'Remotezugriffe');
define('Z_NEW_REMOTE_ACCESS',			'Neuer Remotezugriff');
define('Z_EDIT_REMOTE_ACCESS',			'Remotezugriff bearbeiten');
define('Z_REMOVE_REMOTE_ACCESS',		'Remotezugriff entfernen');

define('Z_IP_ADDRESS',					'IP-Addresss');
define('Z_CURRENT',						'derzeit');

define('Z_USER_NAME',					'Login-Name');
define('Z_USER_DISPLAY_NAME',			'Anzeigename');
define('Z_USER_ORDER_NAME',				'Sortiername');
define('Z_USER_PASSWORD',				'Passwort');
define('Z_USER_GROUP',					'Gruppe');
define('Z_USER_PERMISSIONS',			'Berechtigungen');
define('Z_USER_PROFILE',				'Profil');
define('Z_USER_PROFILE_HIDE',			'verstecken');

// help -----------------------------------------------------------------------

define('Z_HELP',						'Hilfe');
define('Z_HELPTEXT',					'Hilfetext');
define('Z_HELPTEXTS',					'Hilfetexte');
define('Z_NEW_HELPTEXT',				'Neuer Hilfetext');
define('Z_ADD_HELPTEXT',				'Hilfetext hinzufügen');
define('Z_EDIT_HELPTEXT',				'Hilfetext bearbeiten');
define('Z_REMOVE_HELPTEXT',				'Hilfetext entfernen');

define('Z_HELPTEXT_NAME',				'Name');
define('Z_HELPTEXT_PERMISSION',			'Änderungsrechte');
define('Z_HELPTEXT_TITLE',				'Titel');
define('Z_HELPTEXT_CONTENT',			'Inhalt');

// content management ---------------------------------------------------------

define('Z_CONTENTS',					'Inhaltsverwaltung');

define('Z_TEXTBLOCK',					'Textblock');
define('Z_TEXTBLOCKS',					'Textblöcke');
define('Z_NEW_TEXTBLOCK',				'Neuer Textblock');
define('Z_ADD_TEXTBLOCK',				'Textblock hinzufügen');
define('Z_EDIT_TEXTBLOCK',				'Textblock bearbeiten');
define('Z_REMOVE_TEXTBLOCK',			'Textblock entfernen');

define('Z_TEXTBLOCK_NAME',				'Name');
define('Z_TEXTBLOCK_PERMISSION',		'Änderungsrechte');
define('Z_TEXTBLOCK_TITLE',				'Titel');
define('Z_TEXTBLOCK_CONTENT',			'Inhalt');

// images ---------------------------------------------------------------------

define('Z_IMAGE',						'Bild');
define('Z_IMAGES',						'Bilder');
define('Z_UPLOAD_IMAGE',				'Bild hochladen');
define('Z_UPLOAD_IMAGES',				'Bilder hochladen');

// interface ------------------------------------------------------------------

define('Z_OPTIONS',						'Optionen');
define('Z_NEW',							'neu erstellen');
define('Z_ADD',							'hinzufügen');
define('Z_EDIT',						'bearbeiten');
define('Z_REMOVE',						'entfernen');
define('Z_RECOVER',						'wiederherstellen');
define('Z_DELETE',						'löschen');

define('Z_ARE_YOU_SURE',				'Sind Sie sicher?');
define('Z_YES_CONTINUE',				'Ja, weiter');
define('Z_NO_CANCEL',					'Nein, halt');

define('Z_YES',							'Ja');
define('Z_NO',							'Nein');
define('Z_NO_THANKS',					'Nein, danke');
define('Z_OK',							'OK');
define('Z_CANCEL',						'Abbrechen');
define('Z_START',						'Start');
define('Z_CONTINUE',					'Weiter');
define('Z_FINISH',						'Fertigstellen');

define('Z_DOWNLOAD',					'Download');

// data history ---------------------------------------------------------------

define('Z_DATASET',						'Datensatz');
define('Z_DATETIME_FORMAT',				'd.m.Y H:i');
define('Z_DATE_FORMAT',					'd.m.Y');
define('Z_YEAR_MONTH_FORMAT',			'M Y');
define('Z_YEAR_FORMAT',					'Y');
define('Z_DECIMAL_SEPARATOR',			',');
define('Z_THOUSANDS_SEPARATOR',			'.');
define('Z_STAMP_CREATED',				'erstellt');
define('Z_STAMP_MODIFIED',				'bearbeitet');
define('Z_STAMP_ON_DATE',				' am ');
define('Z_STAMP_BY_USER',				' von ');

// errors ---------------------------------------------------------------------

define('Z_SUCCESSFUL',					'erfolgreich');
define('Z_FAILED',						'fehlgeschlagen');

define('Z_ERROR',						'Fehler');
define('Z_ERROR_INVALID_ID',			'Die übergebene ID ist ungültig.');
define('Z_ERROR_ABORTED',				'Ein Fehler ist aufgetreten. Der Vorgang wurde abgebrochen.');
define('Z_ERROR_CONNECTION',			'Die Verbindung zur Datenbank konnte nicht hergestellt werden.');

?>
