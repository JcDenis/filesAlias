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
$this->registerModule(
    'Files alias',
    "Manage aliases of your blog's media",
    'Osku and contributors',
    '0.7',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type' => 'plugin',
        //'support'   => 'http://forum.dotclear.org/viewtopic.php?id=42317',
        'support'    => 'https://github.com/JcDenis/filesAlias',
        'details'    => 'https://plugins.dotaddict.org/dc2/details/filesAlias',
        'repository' => 'https://raw.githubusercontent.com/JcDenis/filesAlias/master/dcstore.xml',
    ]
);
