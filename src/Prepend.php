<?php
/**
 * @brief filesAlias, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Osku and contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {
    return null;
}

Clearbricks::lib()->autoload([
    'filesAliases'  => __DIR__ . '/inc/class.files.alias.php',
    'aliasMedia'    => __DIR__ . '/inc/class.files.alias.php',
    'PallazzoTools' => __DIR__ . '/inc/lib.files.alias.tools.php',
]);

dcCore::app()->__set('filealias', new filesAliases());

dcCore::app()->url->register(
    'filesalias',
    'pub',
    '^pub/(.+)$',
    ['urlFilesAlias','alias']
);
