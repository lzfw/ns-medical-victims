<?php

// TRANSLATION FILE: GERMAN
// last known update: 2014-01-27

setlocale (LC_ALL,'de_DE@euro','de_DE','de','ge');

// ZEFIRO FRAMEWORK -----------------------------------------------------------

define('L_ERROR_404',					'Fehler 404');
define('L_LANGUAGE',					'Sprache');
define('L_SITE_CONTACT',				'Kontakt');
define('L_SITE_NOTICE',					'Impressum');
define('L_PRINT',						'Drucken');
define('L_PRINTABLE_PAGE',				'Druckansicht');
define('L_VERSION',						'Zefiro 4.1.0');

// general phrases ------------------------------------------------------------

define('L_HOME',						'Startseite');
define('L_CONTACT',						'Kontakt');
define('L_EMAIL',						'E-Mail');
define('L_COMMENT',						'Kommentar');
define('L_FORMAT',						'Format');

define('L_SEARCH',						'Suche');
define('L_STATISTICS',       'Statistik');
define('L_QUICK_SEARCH',				'Schnellsuche');
define('L_ADVANCED_SEARCH',				'Erweiterte Suche');
define('L_MODIFY_SEARCH',				'Suche verändern');
define('L_NEW_SEARCH',					'Neue Suche');

// list browser ---------------------------------------------------------------

define('L_RESULTS_NEXT',				'nächste ');
define('L_RESULTS_PREVIOUS',			'vorige ');
define('L_RESULTS_FIRST',				'Anfang');
define('L_RESULTS_LAST',				'Ende');
define('L_OF_TOTAL_RESULTS',			'von');
define('L_RESULTS_ORDER',				'Reihenfolge');
define('L_RESULTS',						'Ergebnisse');
define('L_NO_RESULTS',					'Die Anfrage ergab keine Ergebnisse.');
define('L_ERROR_QUERY',					'Die Anfrage konnte nicht ausgeführt werden.');

// browsing -------------------------------------------------------------------

define('L_FORWARD',						'vor zur nächsten Seite');
define('L_BACK',						'zurück zur vorherigen Seite');

// bookmarks ------------------------------------------------------------------

define('L_BOOKMARK',					'Lesezeichen');
define('L_BOOKMARKS',					'Lesezeichen');
define('L_BOOKMARKS_EMPTY',				'Momentan sind keine Lesezeichen auf der Liste.');
define('L_BOOKMARKS_DUMPED',			'Alle Lesezeichen wurden entfernt.');
define('L_VIEW_BOOKMARKS',				'Lesezeichen öffnen');
define('L_ADD_BOOKMARK',				'Lesezeichen hinzufügen');
define('L_REMOVE_BOOKMARK',				'Lesezeichen entfernen');
define('L_SEND_BOOKMARKS',				'Lesezeichen versenden');
define('L_DUMP_BOOKMARKS',				'Alle Lesezeichen entfernen');
define('L_BOOKMARKS_ASK_EMAIL',			'Bitte geben Sie die E-Mail-Adresse des Empfängers ein.');

// user authentication --------------------------------------------------------

define('L_LOGIN',						'Login');
define('L_LOGOUT',						'Logout');
define('L_LOGIN_PROMPT',				'Bitte geben Sie Ihren Benutzernamen und Ihr Passwort ein.');
define('L_INVALID_LOGIN',				'Ungültiger Login. Bitte überprüfen Sie die Angaben.');
define('L_LOGGED_OUT',					'Ihre Sitzung wurde beendet.');
define('L_AUTHENTICATED_AS',			'angemeldet als');
define('L_SESSION_TIMEOUT',		        'Sitzung endet automatisch um');

// user permissions -----------------------------------------------------------

define('L_PERMISSION_REQUIRED',			'Berechtigung erforderlich');
define('L_PERMISSIONS_INSUFFICIENT',	'Sie haben keine ausreichende Berechtigung, diese Seite aufzurufen.');
define('L_LOGIN_OR_GO_HOME',			'Bitte loggen Sie sich ein oder kehren Sie zur Startseite zurück.');
define('L_RELOGIN_OR_GO_HOME',			'Bitte loggen Sie sich mit einem anderen Account ein, welches über diese Berechtigungen verfügt, oder kehren Sie zur Startseite zurück.');

// admin tools ----------------------------------------------------------------

define('L_ADMIN',						'Systemsteuerung');
define('L_DATABASE',					'Datenbank');
define('L_DATABASE_STATUS',				'Status');
define('L_DATABASE_MAINTENANCE',		'Wartungsaufgaben');
define('L_DATABASE_OPTIMIZE',			'Optimieren');
define('L_DATABASE_NO_TASKS',			'Es stehen keine Wartungsaufgaben an.');
define('L_DATABASE_IMPORT',				'Import');
define('L_DATABASE_IMPORT_OK',			'Der Import der Daten ist abgeschlossen.');
define('L_DATABASE_BACKUP',				'Sicherung');
define('L_DATABASE_BACKUP_OK',			'Eine Sicherung der Daten wurde erstellt.');
define('L_DATABASE_RECOVERY',			'Wiederherstellung');
define('L_DATABASE_RECOVERY_OK',		'Die Wiederherstellung der Daten ist abgeschlossen.');

// user accounts --------------------------------------------------------------

define('L_USERS',						'Nutzer');
define('L_USER_ACCOUNT',				'Nutzerkonto');
define('L_USER_ACCOUNTS',				'Nutzerkonten');
define('L_NEW_USER_ACCOUNT',			'Neues Nutzerkonto');
define('L_EDIT_USER_ACCOUNT',			'Nutzerkonto bearbeiten');
define('L_REMOVE_USER_ACCOUNT',			'Nutzerkonto entfernen');

define('L_REMOTE_ACCESS',				'Remotezugriff');
define('L_REMOTE_ACCESSES',				'Remotezugriffe');
define('L_NEW_REMOTE_ACCESS',			'Neuer Remotezugriff');
define('L_EDIT_REMOTE_ACCESS',			'Remotezugriff bearbeiten');
define('L_REMOVE_REMOTE_ACCESS',		'Remotezugriff entfernen');

define('L_VIEW_LOG',					'Logs');
define('L_DATE_TIME',					'Datum und Uhrzeit');
define('L_OPERATION',					'Operation');
define('L_ENTITY',			    		'Entität');
define('L_RESULT',			    		'Ergebnis');
define('L_ROW_ID',			    		'Datensatz ID');
define('L_DETAILS',			    		'Details');

define('L_IP_ADDRESS',					'IP-Addresss');
define('L_CURRENT',						'derzeit');

define('L_USER_NAME',					'Login-Name');
define('L_USER_DISPLAY_NAME',			'Anzeigename');
define('L_USER_ORDER_NAME',				'Sortiername');
define('L_USER_PASSWORD',				'Passwort');
define('L_USER_GROUP',					'Gruppe');
define('L_USER_PERMISSIONS',			'Berechtigungen');
define('L_USER_PROFILE',				'Profil');
define('L_USER_PROFILE_HIDE',			'verstecken');
define('L_PASSWORD_UPDATE_FAILED',		'Fehler bei der Aktualisierung des Passworts.');

// help -----------------------------------------------------------------------

define('L_HELP',						'Hilfe');
define('L_HELPTEXT',					'Hilfetext');
define('L_HELPTEXTS',					'Hilfetexte');
define('L_NEW_HELPTEXT',				'Neuer Hilfetext');
define('L_ADD_HELPTEXT',				'Hilfetext hinzufügen');
define('L_EDIT_HELPTEXT',				'Hilfetext bearbeiten');
define('L_REMOVE_HELPTEXT',				'Hilfetext entfernen');

define('L_HELPTEXT_NAME',				'Name');
define('L_HELPTEXT_PERMISSION',			'Änderungsrechte');
define('L_HELPTEXT_TITLE',				'Titel');
define('L_HELPTEXT_CONTENT',			'Inhalt');

// content management ---------------------------------------------------------

define('L_CONTENTS',					'Inhaltsverwaltung');

define('L_TEXTBLOCK',					'Textblock');
define('L_TEXTBLOCKS',					'Textblöcke');
define('L_NEW_TEXTBLOCK',				'Neuer Textblock');
define('L_ADD_TEXTBLOCK',				'Textblock hinzufügen');
define('L_EDIT_TEXTBLOCK',				'Textblock bearbeiten');
define('L_REMOVE_TEXTBLOCK',			'Textblock entfernen');

define('L_TEXTBLOCK_NAME',				'Name');
define('L_TEXTBLOCK_PERMISSION',		'Änderungsrechte');
define('L_TEXTBLOCK_TITLE',				'Titel');
define('L_TEXTBLOCK_CONTENT',			'Inhalt');

// images ---------------------------------------------------------------------

define('L_IMAGE',						'Bild');
define('L_IMAGES',						'Bilder');
define('L_UPLOAD_IMAGE',				'Bild hochladen');
define('L_UPLOAD_IMAGES',				'Bilder hochladen');

// interface ------------------------------------------------------------------

define('L_OPTIONS',						'Optionen');
define('L_NEW',							'neu erstellen');
define('L_ADD',							'hinzufügen');
define('L_EDIT',						'bearbeiten');
define('L_REMOVE',						'entfernen');
define('L_RECOVER',						'wiederherstellen');
define('L_DELETE',						'löschen');

define('L_ARE_YOU_SURE',				'Sind Sie sicher?');
define('L_YES_CONTINUE',				'Ja, weiter');
define('L_NO_CANCEL',					'Nein, halt');

define('L_YES',							'Ja');
define('L_NO',							'Nein');
define('L_NO_THANKS',					'Nein, danke');
define('L_OK',							'OK');
define('L_CANCEL',						'Abbrechen');
define('L_START',						'Start');
define('L_CONTINUE',					'Weiter');
define('L_FINISH',						'Fertigstellen');

define('L_DOWNLOAD',					'Download');

// data history ---------------------------------------------------------------

define('L_DATASET',						'Datensatz');
define('L_DATETIME_FORMAT',				'd.m.Y H:i');
define('L_DATE_FORMAT',					'd.m.Y');
define('L_YEAR_MONTH_FORMAT',			'M Y');
define('L_YEAR_FORMAT',					'Y');
define('L_DECIMAL_SEPARATOR',			',');
define('L_THOUSANDS_SEPARATOR',			'.');
define('L_STAMP_CREATED',				'erstellt');
define('L_STAMP_MODIFIED',				'bearbeitet');
define('L_STAMP_ON_DATE',				' am ');
define('L_STAMP_BY_USER',				' von ');

// errors ---------------------------------------------------------------------

define('L_SUCCESSFUL',					'erfolgreich');
define('L_FAILED',						'fehlgeschlagen');

define('L_ERROR',						'Fehler');
define('L_ERROR_INVALID_ID',			'Die übergebene ID ist ungültig.');
define('L_ERROR_ABORTED',				'Ein Fehler ist aufgetreten. Der Vorgang wurde abgebrochen.');
define('L_ERROR_CONNECTION',			'Die Verbindung zur Datenbank konnte nicht hergestellt werden.');

?>
