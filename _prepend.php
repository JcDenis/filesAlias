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

$__autoload['filesAliases'] = dirname(__FILE__).'/inc/class.files.alias.php';
$__autoload['aliasMedia'] = dirname(__FILE__).'/inc/class.files.alias.php';
$__autoload['PallazzoTools'] = dirname(__FILE__).'/inc/lib.files.alias.tools.php';

$core->filealias = new filesAliases($core);

$core->url->register('filesalias',
	'pub',
	'^pub/(.+)$',
	array('urlFilesAlias','alias')
);