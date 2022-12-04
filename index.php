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

$o       = dcCore::app()->filealias;
$aliases = $o->getAliases();
$media   = new dcMedia();
$a       = new aliasMedia();
$part    = $_REQUEST['part'] ?? 'list';

# Update aliases
if (isset($_POST['a']) && is_array($_POST['a'])) {
    try {
        $o->updateAliases($_POST['a']);
        dcAdminNotices::addSuccessNotice(__('Aliases successfully updated.'));
        dcCore::app()->adminurl->redirect('admin.plugin.filesAlias');
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

# New alias
if (isset($_POST['filesalias_url'])) {
    $url = empty($_POST['filesalias_url']) ? PallazzoTools::rand_uniqid() : $_POST['filesalias_url'];

    $target   = $_POST['filesalias_destination'];
    $totrash  = isset($_POST['filesalias_disposable']) ? true : false;
    $password = empty($_POST['filesalias_password']) ? '' : $_POST['filesalias_password'];

    if (preg_match('/^' . preg_quote($media->root_url, '/') . '/', $target)) {
        $target = preg_replace('/^' . preg_quote($media->root_url, '/') . '/', '', $target);
        $found  = $a->getMediaId($target);

        if (!empty($found)) {
            try {
                $o->createAlias($url, $target, $totrash, $password);
                dcAdminNotices::addSuccessNotice(__('Alias for this media created.'));
                dcCore::app()->adminurl->redirect('admin.plugin.filesAlias');
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        } else {
            dcCore::app()->error->add(__('Target is not in media manager.'));
        }
    } else {
        $found = $a->getMediaId($target);

        if (!empty($found)) {
            try {
                $o->createAlias($url, $target, $totrash, $password);
                dcAdminNotices::addSuccessNotice(__('Alias for this media modified.'));
                dcCore::app()->adminurl->redirect('admin.plugin.filesAlias');
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        } else {
            dcCore::app()->error->add(__('Target is not in media manager.'));
        }
    }
}
?>
<html>
<head>
<title><?php echo __('Media sharing'); ?></title>
</head>

<body>
<?php

if ($part == 'new') {
    echo
    dcPage::breadcrumb([
        html::escapeHTML(dcCore::app()->blog->name) => '',
        __('Media sharing')                         => dcCore::app()->adminurl->get('admin.plugin.filesAlias'),
        __('New alias')                             => '',
    ]) .
    dcPage::notices() .
    '<form action="' . dcCore::app()->adminurl->get('admin.plugin.filesAlias') . '" method="post">' .
    '<h3>' . __('New alias') . '</h3>' .
    '<p ><label for="filesalias_destination" class="required">' . __('Destination:') . ' </label>' .
    form::field('filesalias_destination', 70, 255) . '</p>' .
    '<p class="form-note warn">' . __('Destination file must be in media manager.') . '</p>' .
    '<p><label for="filesalias_url">' . __('URL (alias):') . '</label>' .
    form::field('filesalias_url', 70, 255) . '</p>' .
    '<p class="form-note info">' . __('Leave empty to get a randomize alias.') . '</p>' .
    '<p><label for="filesalias_password">' . __('Password:') . '</label> ' .
    form::field('filesalias_password', 70, 255) . '</p>' .
    '<p>' . form::checkbox('filesalias_disposable', 1) .
    '<label for="filesalias_disposable" class="classic">' . __('Disposable') . '</label></p>' .
    '<p>' .
    dcCore::app()->formNonce() .
    form::hidden('part', 'new') .
    '<input type="submit" value="' . __('Save') . '" /></p>' .
    '<p class="form-note">' . sprintf(__('Do not put blog media URL "%s" in fields or it will be removed.'), $media->root_url) . '</p>' .
    '</form>';
} else {
    echo
    dcPage::breadcrumb([
        html::escapeHTML(dcCore::app()->blog->name) => '',
        __('Media sharing')                         => '',
    ]) .
    dcPage::notices() .
    '<p class="top-add"><a class="button add" href="' .
        dcCore::app()->adminurl->get('admin.plugin.filesAlias', ['part' => 'new']) .
    '">' . __('New alias') . '</a></p>';

    if (empty($aliases)) {
        echo '<p>' . __('No alias') . '</p>';
    } else {
        echo
        '<form action="' . dcCore::app()->adminurl->get('admin.plugin.filesAlias') . '" method="post">' .
        '<div class="table-outer">' .
        '<table><thead>' .
        '<caption>' . __('Aliases list') . '</caption>' .
        '<tr>' .
        '<th class="nowrap" scope="col">' . __('Destination') . ' - <ins>' . html::escapeHTML($media->root_url) . '</ins><code>(-?-)</code></th>' .
        '<th class="nowrap" scope="col">' . __('Alias') . ' - <ins>' . dcCore::app()->blog->url . dcCore::app()->url->getBase('filesalias') . '/' . '</ins><code>(-?-)</code></th>' .
        '<th class="nowrap" scope="col">' . __('Disposable') . '</th>' .
        '<th class="nowrap" scope="col">' . __('Password') . '</th>' .
        '</tr></thead><body>';

        foreach ($aliases as $k => $v) {
            $url = dcCore::app()->blog->url . dcCore::app()->url->getBase('filesalias') . '/' . html::escapeHTML($v['filesalias_url']);

            $link = '<a href="' . $url . '">' . __('link') . '</a>';
            $v['filesalias_disposable'] ??= false;
            echo
            '<tr class="line" id="l_' . $k . '">' .
            '<td>' . form::field(['a[' . $k . '][filesalias_destination]'], 40, 255, html::escapeHTML($v['filesalias_destination'])) . '</td>' .
            '<td class="maximal">' . form::field(['a[' . $k . '][filesalias_url]'], 20, 255, html::escapeHTML($v['filesalias_url'])) . '<a href="' . $url . '">' . __('link') . '</a></td>' .
            '<td class="minimal">' . form::checkbox(['a[' . $k . '][filesalias_disposable]'], 1, $v['filesalias_disposable']) . '</td>' .
            '<td class="minimal">' . form::field(['a[' . $k . '][filesalias_password]'], 10, 255, html::escapeHTML($v['filesalias_password'])) . '</td>' .
            '</tr>';
        }

        echo '</tobdy></table></div>' .
        '<p class="form-note">' . __('To remove a link, empty its alias or destination.') . '</p>' .
        '<p>' . dcCore::app()->formNonce() . form::hidden('part', 'list') .
        '<input type="submit" value="' . __('Update') . '" /></p>' .
        '</form>';
    }
}

dcPage::helpBlock('filesAlias');
?>
</body>
</html>