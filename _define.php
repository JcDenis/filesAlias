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
if (!defined('DC_RC_PATH')) { return; }

$this->registerModule(
	/* Name */				'filesAlias',
	/* Description*/		"Manage aliases of your blog's medias",
	/* Author */			"Osku & Pierre Van Glabeke",
	/* Version */			'0.6',
	array(
		'permissions' => 'contentadmin',
		'type' => 'plugin',
		'dc_min' => '2.7',
		'support' => 'http://forum.dotclear.org/viewtopic.php?id=42317',
		'details' => 'http://plugins.dotaddict.org/dc2/details/filesAlias'
		)
);