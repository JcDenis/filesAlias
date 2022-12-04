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
if (!defined('DC_CONTEXT_ADMIN')) {
    return null;
}

dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Media sharing'),
    dcCore::app()->adminurl->get('admin.plugin.filesAlias'),
    urldecode(dcPage::getPF('filesAlias/icon.svg')),
    preg_match('/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.filesAlias')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]), dcCore::app()->blog->id)
);

dcCore::app()->addBehavior('adminDashboardFavoritesV2', function (dcFavorites $favs) {
    $favs->register('filesAlias', [
        'title'       => __('Media sharing'),
        'url'         => dcCore::app()->adminurl->get('admin.plugin.filesAlias'),
        'small-icon'  => dcPage::getPF('filesAlias/icon.svg'),
        'large-icon'  => dcPage::getPF('filesAlias/icon.svg'),
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
    ]);
});
