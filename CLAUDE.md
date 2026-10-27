# ns-medical-victims (zefiro/flotilla curation interface)

Data curation interface of the NMV database. Deployed as the `zefiro` submodule of
`../ns-medical-victims-docker` (php:8.4-apache-bookworm, MariaDB 10.4); the public site is
`../ns-medical-victims-public`.

## PHP
- Target PHP 8.4, keep 8.2 compatible. Upgraded from 7.2 in branch `php-8.4-upgrade`.
- Error display is set only in custom/config.php (production: off). config.default.php turns
  it on with `E_ALL` for development, so keep pages free of warnings and deprecations.
- Undefined constants are fatal on PHP 8. Constants live in zefiro/translations,
  custom/translations, flotilla/translations and flotilla/classes/*.dict.php.
- Declare properties on classes; dynamic properties are deprecated since 8.2.
- mysqli runs with `mysqli_report(MYSQLI_REPORT_OFF)`; the code checks return values.
- Production config (`custom/config.php`, not in git) is linked in by CI from
  /opt/config on the server.

## Security
- `DBI::requireUserPermission()` / `requireUserAuthentication()` / `denyUserPermission()` redirect
  and `exit`. Call them before any output.
- Request values in SQL: `escapeSQL()` (zefiro/lib/url.php) inside '...' literals, applied after
  the wildcard `str_replace`; `(int)` for IDs. `DBI::setUserVar()` only accepts column names
  for `sort` and ASC/DESC lists for `order`, since both go into ORDER BY unquoted.
- Request values in HTML: `htmlspecialchars()`; in links: `http_build_query()`/`urlencode()`;
  in onclick handlers: `Button::HTMLOnclickLocation()` (JSON + HTML escaping). `skip` must be
  digits (`DBI::setUserVar()`), flotilla's "last page" only accepts referers from this site.
- Database values in HTML: `safeHTML()` (zefiro/lib/html.php; keeps entities and simple tags
  without attributes), page titles/breadcrumbs: `safeTitleHTML()`. Trusted (admin-only) rich
  content, printed raw: textblocks, the public-site `website_*` tables, user profiles.
- Never pass SQL through URLs. Result pages hand their WHERE clause to nmv_export.php via
  `registerExportWhereClause()` (zefiro/lib/url.php, stored in the session); links carry only
  `export-id`.

## Testing
No test suite. The PHP upgrade was verified by a throwaway Docker stack (mariadb:10.4 with the
schema plus synthetic data generated from information_schema, a test user, and the app
mounted with a test custom/config.php). A crawler logged in, fetched ~1650 URLs and replayed
~560 edit forms on 7.2 and on 8.x, and the bodies and the resulting DB dumps were compared.
Row order of ties differs between runs of the same version; compare those order-insensitively.
