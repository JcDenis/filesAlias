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
declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use dcCore;
use dcMedia;
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Html\Form\{
    Checkbox,
    Form,
    Hidden,
    Input,
    Label,
    Note,
    Para,
    Submit,
    Text
};
use Exception;

class Manage extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && !is_null(dcCore::app()->auth) && !is_null(dcCore::app()->blog) // nullsafe
            && dcCore::app()->auth->check(
                dcCore::app()->auth->makePermissions([
                    dcCore::app()->auth::PERMISSION_ADMIN,
                ]),
                dcCore::app()->blog->id
            );

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // nullsafe
        if (is_null(dcCore::app()->blog) || is_null(dcCore::app()->adminurl)) {
            return false;
        }

        if (!(dcCore::app()->media instanceof dcMedia)) {
            dcCore::app()->media = new dcMedia();
        }

        // Update aliases
        if (isset($_POST['a']) && is_array($_POST['a'])) {
            try {
                Utils::updateAliases($_POST['a']);
                dcPage::addSuccessNotice(__('Aliases successfully updated.'));
                dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        // New alias
        if (isset($_POST['filesalias_url'])) {
            $url = empty($_POST['filesalias_url']) ? PallazzoTools::rand_uniqid() : $_POST['filesalias_url'];

            $target   = $_POST['filesalias_destination'];
            $totrash  = isset($_POST['filesalias_disposable']) ? true : false;
            $password = empty($_POST['filesalias_password']) ? '' : $_POST['filesalias_password'];

            if (preg_match('/^' . preg_quote(dcCore::app()->media->root_url, '/') . '/', $target)) {
                $target = preg_replace('/^' . preg_quote(dcCore::app()->media->root_url, '/') . '/', '', $target);
                $found  = Utils::getMediaId($target);

                if (!empty($found)) {
                    try {
                        Utils::createAlias($url, $target, $totrash, $password);
                        dcPage::addSuccessNotice(__('Alias for this media created.'));
                        dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
                    } catch (Exception $e) {
                        dcCore::app()->error->add($e->getMessage());
                    }
                } else {
                    dcCore::app()->error->add(__('Target is not in media manager.'));
                }
            } else {
                $found = Utils::getMediaId($target);

                if (!empty($found)) {
                    try {
                        Utils::createAlias($url, $target, $totrash, $password);
                        dcPage::addSuccessNotice(__('Alias for this media modified.'));
                        dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
                    } catch (Exception $e) {
                        dcCore::app()->error->add($e->getMessage());
                    }
                } else {
                    dcCore::app()->error->add(__('Target is not in media manager.'));
                }
            }
        }

        return true;
    }

    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        dcPage::openModule(My::name());

        if (($_REQUEST['part'] ?? '') == 'new') {
            self::displayAliasForm();
        } else {
            self::displayAliasList();
        }

        dcPage::helpBlock('filesAlias');

        dcPage::closeModule();
    }

    private static function displayAliasForm(): void
    {
        // nullsafe
        if (is_null(dcCore::app()->blog) || is_null(dcCore::app()->adminurl) || is_null(dcCore::app()->media)) {
            return;
        }

        echo
        dcPage::breadcrumb([
            Html::escapeHTML(dcCore::app()->blog->name) => '',
            My::name()                                  => dcCore::app()->adminurl->get('admin.plugin.' . My::id()),
            __('New alias')                             => '',
        ]) .
        dcPage::notices() .
        (new Form('filesalias_new'))->action(dcCore::app()->adminurl->get('admin.plugin.' . My::id()))->method('post')->fields([
            (new Text('h3', Html::escapeHTML(__('New alias')))),
            (new Note())->text(sprintf(__('Do not put blog media URL "%s" in fields or it will be removed.'), dcCore::app()->media->root_url))->class('form-note'),
            // destination
            (new Para())->items([
                (new Label(__('Destination:')))->for('filesalias_destination')->class('required'),
                (new Input('filesalias_destination'))->size(70)->maxlenght(255),
            ]),
            (new Note())->text(__('Destination file must be in media manager.'))->class('form-note'),
            // url
            (new Para())->items([
                (new Label(__('URL (alias):')))->for('filesalias_url')->class('required'),
                (new Input('filesalias_url'))->size(70)->maxlenght(255),
            ]),
            (new Note())->text(__('Leave empty to get a randomize alias.'))->class('form-note'),
            // password
            (new Para())->items([
                (new Label(__('Password:')))->for('filesalias_password')->class('required'),
                (new Input('filesalias_password'))->size(70)->maxlenght(255),
            ]),
            // disposable
            (new Para())->items([
                (new Checkbox('filesalias_disposable', false))->value(1),
                (new Label(__('Disposable'), Label::OUTSIDE_LABEL_AFTER))->for('filesalias_disposable')->class('classic'),
            ]),
            // submit
            (new Para())->items([
                (new Submit(['save']))->value(__('Save')),
                (new Hidden(['part'], 'new')),
                (new Text('', dcCore::app()->formNonce())),
            ]),
        ])->render();
    }

    private static function displayAliasList(): void
    {
        // nullsafe
        if (is_null(dcCore::app()->blog) || is_null(dcCore::app()->adminurl) || is_null(dcCore::app()->media)) {
            return;
        }

        $aliases = Utils::getAliases();

        echo
        dcPage::breadcrumb([
            Html::escapeHTML(dcCore::app()->blog->name) => '',
            My::name()                                  => '',
        ]) .
        dcPage::notices() .
        '<p class="top-add"><a class="button add" href="' .
            dcCore::app()->adminurl->get('admin.plugin.' . My::id(), ['part' => 'new']) .
        '">' . __('New alias') . '</a></p>';

        if ($aliases->isEmpty()) {
            echo '<p>' . __('No alias') . '</p>';
        } else {
            $lines = '';
            $i     = 0;
            while ($aliases->fetch()) {
                $url = dcCore::app()->blog->url . dcCore::app()->url->getBase('filesalias') . '/' . Html::escapeHTML($aliases->f('filesalias_url'));

                $lines .= '<tr class="line" id="l_' . $i . '">' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_destination]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($aliases->f('filesalias_destination')))->render() .
                '</td>' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_url]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($aliases->f('filesalias_url')))->render() .
                '<a href="' . $url . '">' . __('link') . '</a></td>' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_password]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($aliases->f('filesalias_password')))->render() .
                '</td>' .
                '<td class="maximal">' .
                (new Checkbox(['a[' . $i . '][filesalias_disposable]'], (bool) $aliases->f('filesalias_disposable')))->value(1)->render() .
                '</td>' .
                '</tr>';
                $i++;
            }

            echo
            (new Form('filesalias_list'))->action(dcCore::app()->adminurl->get('admin.plugin.' . My::id()))->method('post')->fields([
                (new Text(
                    '',
                    '<div class="table-outer">' .
                    '<table><thead>' .
                    '<caption>' . __('Aliases list') . '</caption>' .
                    '<tr>' .
                    '<th class="nowrap" scope="col">' . __('Destination') . ' - <ins>' . Html::escapeHTML(dcCore::app()->media->root_url) . '</ins><code>(-?-)</code></th>' .
                    '<th class="nowrap" scope="col">' . __('Alias') . ' - <ins>' . dcCore::app()->blog->url . dcCore::app()->url->getBase('filesalias') . '/' . '</ins><code>(-?-)</code></th>' .
                    '<th class="nowrap" scope="col">' . __('Password') . '</th>' .
                    '<th class="nowrap" scope="col">' . __('Disposable') . '</th>' .
                    '</tr></thead><body>' .
                    $lines .
                    '</tobdy></table></div>'
                )),
                (new Para())->items([
                    (new Submit(['save']))->value(__('Update')),
                    (new Hidden(['part'], 'list')),
                    (new Text('', dcCore::app()->formNonce())),
                ]),
                (new Note())->text(__('To remove a link, empty its alias or destination.'))->class('form-note'),
            ])->render();
        }
    }
}
