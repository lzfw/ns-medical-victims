-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Erstellungszeit: 16. Juni 2014 um 11:08
-- Server Version: 5.5.31
-- PHP-Version: 5.3.3-7+squeeze19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `z_textblocks`
--

CREATE TABLE IF NOT EXISTS `z_textblocks` (
  `textblock_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` date NOT NULL,
  `created_user_id` int(6) unsigned NOT NULL DEFAULT '0',
  `modified_date` date NOT NULL,
  `modified_user_id` int(6) unsigned NOT NULL DEFAULT '0',
  `title_en` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `title_de` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `content_en` text COLLATE utf8_unicode_ci NOT NULL,
  `content_de` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`textblock_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=100037 ;

--
-- Daten für Tabelle `z_textblocks`
--

INSERT INTO `z_textblocks` (`textblock_id`, `name`, `permission`, `created_date`, `created_user_id`, `modified_date`, `modified_user_id`, `title_en`, `title_de`, `content_en`, `content_de`) VALUES
(100004, 'search', 'admin', '2010-12-23', 100004, '2013-01-22', 100004, '', '', '**Truncation:** You can use the asterisk (*) for truncation in both languages. For example, a research for __αγα*__ would return __αγαθος__, __αγαμαι__ and __αγαν__.\r\n\r\n**Combination:** If more than one field is filled in, the research returns only results that match all those fields (conjunction).\r\n\r\n**Entering Diacritics and Vowels:** Note that Greek diacritics are selected __before__ the base letter, while Arabic vowels are selected __after__ the consonant letter.\r\n\r\n**Greek Diacritics:** If no diacritics are applied, the research covers any combination. A research for η* would also return words starting with ἠ, ἦ or ἥ.\r\n\r\n**Arabic Vowels:** Vowels are treated as indistinct elements. A research for سبب would also return سَبَبٌ, and vice versa.\r\n\r\n**Arabic Roots:** Roots are recorded in Latin transcription. Diacritics are always distinctive, as each symbol corresponds to one arabic letter.\r\n', '**Truncation:** You can use the asterisk (*) for truncation in both languages. For example, a research for __αγα*__ would return __αγαθος__, __αγαμαι__ and __αγαν__.\r\n\r\n**Combination:** If more than one field is filled in, the research returns only results that match all those fields (conjunction).\r\n\r\n**Entering Diacritics and Vowels:** Note that Greek diacritics are selected __before__ the base letter, while Arabic vowels are selected __after__ the consonant letter.\r\n\r\n**Greek Diacritics:** If no diacritics are applied, the research covers any combination. A research for η* would also return words starting with ἠ, ἦ or ἥ.\r\n\r\n**Arabic Vowels:** Vowels are treated as indistinct elements. A research for سبب would also return سَبَبٌ, and vice versa.\r\n\r\n**Arabic Roots:** Roots are recorded in Latin transcription. Diacritics are always distinctive, as each symbol corresponds to one arabic letter.\r\n'),
(100007, 'bookmarks', 'admin', '2010-12-23', 100004, '2013-01-22', 100004, '', '', 'This is a list of your bookmarks.', 'Dies ist eine Liste Ihrer Lesezeichen.'),
(100008, 'bookmarks_send', 'admin', '2010-12-23', 100004, '2013-01-22', 100004, '', '', 'You can enter an e-mail address to send your bookmarks to a mailbox.\r\n\r\nOptionally, you can enter a comment, making it easier to distinguish between different research sessions.', ''),
(100018, 'simple_markup', 'system', '2011-01-03', 100004, '2013-01-22', 100004, 'How to use Simple Markup', '', 'You can use the following markup symbols:\r\n\r\n**italics:** a word surrounded by double underscores is rendered in italics.\r\n_~_lorem ipsum_~_ = __lorem ipsum__\r\n\r\n**bold text:** a word surrounded by double stars is rendered in bold type.\r\n*~*lorem ipsum*~* = **lorem ipsum**\r\n\r\n**links:** a text surrounded by double square brackets is rendered as a hyperlink. A text that replaces the url must be placed directly after the first bracket.\r\n[~[http://example.com]~] = [[http://example.com]]\r\n[example [http://example.com]~] = [example [http://example.com]]\r\n\r\n**paragraphs:** any single line feed is rendered as a line break. two or more line feeds appear as a paragraph.\r\n\r\n**special symbols**\r\ndash: -~- = --\r\nbullet: :~: = ::\r\ndiamond: +~+ = ++\r\ncopyright: $~C = $C\r\npilcrow: $~P = $P\r\n\r\n**HTML** is also allowed.\r\n', ''),
(100024, 'results', 'admin', '2011-01-07', 100004, '2013-01-22', 100004, '', '', 'You can refine the list of results by modifying your search.\r\n', ''),
(100036, 'z_database', 'system', '2014-02-03', 0, '0000-00-00', 0, '', '', '', 'Dies ist die Datenbankverwaltung.\r\n\r\nAls Administrator können Sie Statusabfragen und Wartungsaufgaben ausführen.\r\n\r\nFür weitergehende Operationen wie Backup, Wiederherstellung und Import benötigen Sie Systemrechte.'),
(100020, 'database_backup', 'system', '2011-01-03', 100004, '2013-01-25', 100004, '', '', 'Regularly database backups are guaranteed by the BBAW.\r\n\r\nIt is not necessary to store backups on your own.\r\n\r\nNevertheless you can download a database dump file here, if you really want to. Be warned: this might take some time and the file is quite large.\r\n', ''),
(100021, 'database_recover', 'system', '2011-01-03', 100004, '2013-01-25', 100004, 'explains the database recovery function', '', 'This function resets the database to a previously saved backup copy. It should only be used in case of data loss or massive misusage. All current data will be overwritten.\r\n', ''),
(100022, 'advanced_search', 'system', '2011-01-03', 100004, '2013-01-18', 100004, '', '', 'You can also browse through the list of sources, filecards and words, or explore the database by using the more detailed [<img class="icon" src="icons/fugue/question-balloon.png"> Search Form [search.php]].\r\n', ''),
(100027, 'contact', 'admin', '2011-08-29', 100004, '2011-08-30', 100004, 'Contact Information', '', 'For any information about the project and its content, please write an email to Yury Arzhanov ([[mailto:yury.arzhanov@ruhr-uni-bochum.de]]).\r\n\r\nPlease consider also our [publication details page [impressum.php]] for further information about this website.\r\n', ''),
(100033, 'z_contents', 'system', '2014-01-27', 100002, '0000-00-00', 0, '', 'Inhaltsverwaltung', '', 'Auf dieser Seite können Sie alles verwalten, was mit den Inhalten der Website zu tun hat.'),
(100034, 'z_admin', 'system', '2014-01-27', 100002, '2014-01-27', 100002, 'Administration Help', 'Über die Systemsteuerung', '', 'Hier können Sie die Nutzer verwalten, Remote-Zugriffe definieren und die Datenbank warten.'),
(100030, 'home', 'admin', '2013-01-25', 100004, '2014-01-27', 100002, '', 'Startseite', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.'),
(100031, 'about', 'admin', '2013-01-25', 100004, '2013-01-25', 100004, '', '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.'),
(100032, 'notice', 'admin', '2013-01-25', 100004, '2013-01-25', 100004, '', '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `z_users`
--

CREATE TABLE IF NOT EXISTS `z_users` (
  `user_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `remote` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `order_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `group` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `profile_hide` tinyint(1) NOT NULL DEFAULT '1',
  `profile_en` text COLLATE utf8_unicode_ci NOT NULL,
  `profile_de` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=100014 ;

--
-- Daten für Tabelle `z_users`
--

INSERT INTO `z_users` (`user_id`, `name`, `password`, `remote`, `display_name`, `order_name`, `group`, `permissions`, `profile_hide`, `profile_en`, `profile_de`) VALUES
(100001, 'anonymous', '', '', 'Anonymous', 'anonymous', 'www', '', 1, '', ''),
(100002, 'system', 'system', '', 'System Administrator', 'system', 'bbaw', 'view, edit, admin, system', 1, '', ''),
(100003, 'admin', 'admin', '', 'Administrator', 'admin', 'bbaw', 'view, edit, admin', 1, '', ''),
(100004, 'editor', 'editor', '', 'Editor', 'editor', 'www', 'view, edit', 1, '', ''),
(100005, 'zefiro', 'zefiro', '', 'Zefiro', 'zefiro', 'www', 'view', 1, '', ''),
(100006, 'viewer', 'viewer', '', 'Viewer', 'viewer', 'www', 'view', 1, '', ''),
(100008, 'localhost', '', '127.0.0.1', 'Local Host', 'localhost', 'localhost', '', 1, '', '');
