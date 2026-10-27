# ns-medical-victims (zefiro/flotilla curation interface)

Data curation interface of the NMV database. Deployed as the `zefiro` submodule of
`../ns-medical-victims-docker` (php:8.4-apache-bookworm, MariaDB 10.4); the public site is
`../ns-medical-victims-public`.

## PHP
- Target PHP 8.4, keep 8.2 compatible. Upgraded from 7.2 in branch `php-8.4-upgrade`.
- `display_errors=1` / `E_ALL` is hard-coded (custom/config.default.php, zefiro/lib/layout.php,
  flotilla/ini.php), so every warning or deprecation shows up in the UI. Keep pages free of them.
- Undefined constants are fatal on PHP 8. Constants live in zefiro/translations,
  custom/translations, flotilla/translations and flotilla/classes/*.dict.php.
- Declare properties on classes; dynamic properties are deprecated since 8.2.
- mysqli runs with `mysqli_report(MYSQLI_REPORT_OFF)`; the code checks return values.
- Production config (`custom/config.php`, not in git) is linked in by CI from
  /opt/config on the server.

## Dead pages (already broken on PHP 7.2, left untouched)
bookmarks.php, bookmarks_sendmail.php, results.php (missing setup/ini.php),
z_upload_image.php, z_error_sendmail.php, z_database_recovery.php (missing templates/ini.php),
zefiro/lib/zefiro.php getDateUserStamp_HTML()/historicalDateFormat() (undefined Z_*_FORMAT).

## Testing
No test suite. The PHP upgrade was verified by a throwaway Docker stack (mariadb:10.4 with the
schema plus synthetic data generated from information_schema, a test user, and the app
mounted with a test custom/config.php). A crawler logged in, fetched ~1650 URLs and replayed
~560 edit forms on 7.2 and on 8.x, and the bodies and the resulting DB dumps were compared.
Row order of ties differs between runs of the same version; compare those order-insensitively.
