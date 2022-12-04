<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of filesAlias, a plugin for Dotclear 2.
# 
# Copyright (c) 2009-2015 Osku & Pierre Van Glabeke
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

$_menu['Blog']->addItem(__('Medias sharing'),
	$core->adminurl->get('admin.plugin.filesAlias'),
	dcPage::getPF('filesAlias/icon.png'),
	preg_match('/plugin.php(.*)$/',$_SERVER['REQUEST_URI']) && !empty($_REQUEST['p']) && $_REQUEST['p']=='filesAlias',
	$core->auth->check('contentadmin',$core->blog->id));

$core->addBehavior('adminDashboardFavorites','filesAliasDashboardFavorites');

function filesAliasDashboardFavorites($core,$favs)
{
	$favs->register('filesAlias', array(
		'title' => __('Medias sharing'),
		'url' =>  $core->adminurl->get('admin.plugin.filesAlias'),
		'small-icon' => dcPage::getPF('filesAlias/icon.png'),
		'large-icon' => dcPage::getPF('filesAlias/icon_b.png'),
		'permissions' => 'usage,contentadmin'
	));
}
