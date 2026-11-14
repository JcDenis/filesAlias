# filesAlias

Notes for AI assistants working on the Dotclear plugin filesAlias.

**Read first** the shared conventions of the author's Dotclear modules:
https://raw.githubusercontent.com/JcDenis/gh2da/master/CONVENTIONS.md
(if a web tool refuses the address, use `curl -s` on it from the shell).
The rules below take precedence for this module.

## Module

Create public aliases of your blog's media. Alias can be passworded and/or disposable.

Admin page. Public side: URL handlers.

Required Dotclear and PHP versions: see `_define.php`.

## Structure

* `src/AliasRow.php`
* `src/Backend.php`: admin side: menu entry, favorites, behaviors registration.
* `src/Frontend.php`: public side: behaviors, template tags and URL handlers registration.
* `src/FrontendTemplate.php`: template tags.
* `src/Install.php`: install and update, database structure (a change needs a migration here).
* `src/Manage.php`: admin page of the module.
* `src/My.php`: module helpers (`My::id()`, settings, permissions).
* `src/PallazzoTools.php`: adapted from Enrico Pallazzo class.
* `src/Prepend.php`: loaded on every request: autoload, URL handlers, permission types.
* `src/Uninstall.php`: cleaners for the plugin Uninstaller.
* `src/UrlHandler.php`: frontend URL handler class.
* `src/Utils.php`: helpers.
* `default-templates/`: public templates.
* Database table `filesalias`.
* URL handlers: `filesalias`.
* Behaviors used: `adminDashboardFavoritesV2`.

## Pitfalls

* Table structure changes need a migration in `src/Install.php` (version compare), and must work on MySQL, PostgreSQL and SQLite.

## Testing

* Check the admin page, the public URLs, the admin help, in English and French.
* CI browses the common admin and public pages; extra pages can be listed in `.github/dotclear-test.txt`.
