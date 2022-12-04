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

$page_title = __('Medias sharing');

$o = $core->filealias;
$aliases = $o->getAliases();
$media = new dcMedia($core);
$a= new aliasMedia($core);

# Update aliases
if (isset($_POST['a']) && is_array($_POST['a']))
{
	try {
		$o->updateAliases($_POST['a']);
		http::redirect($p_url.'&up=1');
	} catch (Exception $e) {
		$core->error->add($e->getMessage());
	}
}

# New alias
if (isset($_POST['filesalias_url']))
{
	$url = empty($_POST['filesalias_url']) ? PallazzoTools::rand_uniqid() : $_POST['filesalias_url'];

	$target = $_POST['filesalias_destination'];
	$totrash = isset ($_POST['filesalias_disposable']) ? true : false;
	$password = empty($_POST['filesalias_password'])? '' : $_POST['filesalias_password'];
	
	if (preg_match('/^'.preg_quote($media->root_url,'/').'/',$target)) {
		$target = preg_replace('/^'.preg_quote($media->root_url,'/').'/','',$target);
		$media = $a->getMediaId($target);

		if (!empty($media))
		{
			try {
				$o->createAlias($url,$target,$totrash,$password);
				http::redirect($p_url.'&created=1');
			} catch (Exception $e) {
				$core->error->add($e->getMessage());
			}
		}
		else
		{
			$core->error->add(__('Target is not in medias manager.'));
		}
	}
	else
	{
		$media = $a->getMediaId($target);

		if (!empty($media))
		{
			try {
				$o->createAlias($url,$target,$totrash,$password);
				http::redirect($p_url.'&created=1');
			} catch (Exception $e) {
				$core->error->add($e->getMessage());
			}
		}
		else
		{
			$core->error->add(__('Target is not in medias manager.'));
		}
	}
}
?>
<html>
<head>
	<title><?php echo $page_title; ?></title>
</head>

<body>
<?php

echo dcPage::breadcrumb(
	array(
		html::escapeHTML($core->blog->name) => '',
		__('Medias sharing') => ''
	)).
	dcPage::notices();

?>
<?php
if (!empty($_GET['up'])) {
  dcPage::success(__('Aliases successfully updated.'));
}

if (!empty($_GET['created'])) {
  dcPage::success(__('Alias for this media created.'));
}

if (!empty($_GET['modified'])) {
  dcPage::success(__('Configuration successfully updated.'));
}

if (empty($aliases))
{
	echo '<p>'.__('No alias').'</p>';
}
else
{
	$root_url = html::escapeHTML($media->root_url);
	echo
	'<form action="'.$p_url.'" method="post">'.
	'<div class="table-outer">'.
	'<table ><thead>
	<caption class="as_h3">'.__('Aliases list').'</caption>
	<tr class="line">'.
	'<th>'.__('Destination').' - <ins>'.$root_url.'</ins><code>(-?-)</code></th>'.
	'<th>'.__('Alias').' - <ins>'.$core->blog->url.$core->url->getBase('filesalias').'/'.'</ins><code>(-?-)</code></th>'.
	'<th>'.__('Disposable').'</th>'.
	'<th>'.__('Password').'</th>'.
	'</tr></thead><body>';
	
	foreach ($aliases as $k => $v)
	{
		$url = $core->blog->url.$core->url->getBase('filesalias').'/'.html::escapeHTML($v['filesalias_url']);
		
		$link = '<a href="'.$url.'">'.__('link').'</a>';
		$v['filesalias_disposable'] = isset ($v['filesalias_disposable']) ? $v['filesalias_disposable'] : false;
		echo
		'<tr class="line">'.
		'<td class="row">'.form::field(array('a['.$k.'][filesalias_destination]'),40,255,html::escapeHTML($v['filesalias_destination'])).'</td>'.
		'<td class="row">'.form::field(array('a['.$k.'][filesalias_url]'),20,255,html::escapeHTML($v['filesalias_url'])).'<a href="'.$url.'">'.__('link').'</a></td>'.
		'<td class="row">'.form::checkbox(array('a['.$k.'][filesalias_disposable]'),1,$v['filesalias_disposable']).'</td>'.
		'<td>'.form::field(array('a['.$k.'][filesalias_password]'),10,255,html::escapeHTML($v['filesalias_password'])).'</td>'.
		'</tr>';
	}
	
	echo '</tobdy></table>'.
	'<p class="form-note">'.__('To remove a link, empty its alias or destination.').'</p>'.
	'<p>'.$core->formNonce().
	'<input type="submit" value="'.__('Update').'" /></p>'.
		'</div>'.
	'</form>';
}

echo
'<form action="'.$p_url.'" method="post">
<div class="fieldset">
<h3>'.__('New alias').'</h3>
<p ><label for="filesalias_destination" class="required">
<abbr title="'.__('Required field').'">*</abbr> '.
__('Destination:').' </label>'.form::field('filesalias_destination',70,255).'
</p>
<p class="form-note warn">'.__('Destination file must be in media manager.').'</p>
<p class="form-note">'.sprintf(__('Root URL "<code>%s</code>" will be automatically removed.'),$media->root_url).'</p>
<p class="field"><label for="filesalias_url">'.
__('URL (alias):').' '.form::field('filesalias_url',70,255).
'</label></p>
<p class="form-note info">'.__('Leave empty to get a randomize alias.').'</p>
<p>'.form::checkbox('filesalias_disposable',1).'<label for="filesalias_disposable" class="classic">'.
__('Disposable').
'</label></p>
<p class="field"><label for="filesalias_password">'.
__('Password:').' '.form::field('filesalias_password',70,255).
'</label></p>
<p>'.$core->formNonce().'<input type="submit" value="'.__('Save').'" /></p>
</div>
</form>';

dcPage::helpBlock('filesAlias');
?>
</body>
</html>