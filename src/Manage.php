<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Core\Backend\{
    Notices,
    Page
};
use Dotclear\Core\Process;
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

/**
 * @brief       filesAlias manage class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Manage extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    public static function process(): bool
    {
        if (!self::status()
            || !APP::blog()->isDefined()
        ) {
            return false;
        }

        // Update aliases
        if (isset($_POST['a']) && is_array($_POST['a'])) {
            try {
                Utils::updateAliases($_POST['a']);
                Notices::addSuccessNotice(__('Aliases successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        // New alias
        if (isset($_POST['filesalias_url'])) {
            $url = empty($_POST['filesalias_url']) ? PallazzoTools::rand_uniqid() : $_POST['filesalias_url'];

            $target   = $_POST['filesalias_destination'];
            $totrash  = isset($_POST['filesalias_disposable']) ? true : false;
            $password = empty($_POST['filesalias_password']) ? '' : $_POST['filesalias_password'];

            if (preg_match('/^' . preg_quote(App::media()->root_url, '/') . '/', $target)) {
                $target = preg_replace('/^' . preg_quote(App::media()->root_url, '/') . '/', '', $target);
                $found  = Utils::getMediaId($target);

                if (!empty($found)) {
                    try {
                        Utils::createAlias($url, $target, $totrash, $password);
                        Notices::addSuccessNotice(__('Alias for this media created.'));
                        My::redirect();
                    } catch (Exception $e) {
                        App::error()->add($e->getMessage());
                    }
                } else {
                    App::error()->add(__('Target is not in media manager.'));
                }
            } else {
                $found = Utils::getMediaId($target);

                if (!empty($found)) {
                    try {
                        Utils::createAlias($url, $target, $totrash, $password);
                        Notices::addSuccessNotice(__('Alias for this media modified.'));
                        My::redirect();
                    } catch (Exception $e) {
                        App::error()->add($e->getMessage());
                    }
                } else {
                    App::error()->add(__('Target is not in media manager.'));
                }
            }
        }

        return true;
    }

    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        Page::openModule(My::name());

        if (($_REQUEST['part'] ?? '') == 'new') {
            self::displayAliasForm();
        } else {
            self::displayAliasList();
        }

        Page::helpBlock('filesAlias');

        Page::closeModule();
    }

    private static function displayAliasForm(): void
    {
        if (!App::blog()->isDefined()) {
            return;
        }

        echo
        Page::breadcrumb([
            Html::escapeHTML(App::blog()->name()) => '',
            My::name()                            => My::manageUrl(),
            __('New alias')                       => '',
        ]) .
        Notices::getNotices() .
        (new Form('filesalias_new'))->action(My::manageUrl())->method('post')->fields([
            (new Text('h3', Html::escapeHTML(__('New alias')))),
            (new Note())->text(sprintf(__('Do not put blog media URL "%s" in fields or it will be removed.'), App::media()->root_url))->class('form-note'),
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
                ... My::hiddenFields(['part' => 'new']),
            ]),
        ])->render();
    }

    private static function displayAliasList(): void
    {
        if (!App::blog()->isDefined()) {
            return;
        }

        $aliases = Utils::getAliases();

        echo
        Page::breadcrumb([
            Html::escapeHTML(App::blog()->name()) => '',
            My::name()                            => '',
        ]) .
        Notices::getNotices() .
        '<p class="top-add"><a class="button add" href="' .
            My::manageUrl(['part' => 'new']) .
        '">' . __('New alias') . '</a></p>';

        if ($aliases->isEmpty()) {
            echo '<p>' . __('No alias') . '</p>';
        } else {
            $lines = '';
            $i     = 0;
            while ($aliases->fetch()) {
                $url         = is_string($aliases->f('filesalias_url')) ? $aliases->f('filesalias_url') : '';
                $destination = is_string($aliases->f('filesalias_destination')) ? $aliases->f('filesalias_destination') : '';
                $password    = is_string($aliases->f('filesalias_password')) ? $aliases->f('filesalias_password') : '';
                $disposable  = !empty($aliases->f('filesalias_disposable'));
                $full        = App::blog()->url() . App::url()->getBase('filesalias') . '/' . Html::escapeHTML($url);

                $lines .= '<tr class="line" id="l_' . $i . '">' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_destination]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($destination))->render() .
                '</td>' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_url]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($url))->render() .
                '<a href="' . $full . '">' . __('link') . '</a></td>' .
                '<td>' .
                (new Input(['a[' . $i . '][filesalias_password]']))->size(50)->maxlenght(255)->value(Html::escapeHTML($password))->render() .
                '</td>' .
                '<td class="maximal">' .
                (new Checkbox(['a[' . $i . '][filesalias_disposable]'], $disposable))->value(1)->render() .
                '</td>' .
                '</tr>';
                $i++;
            }

            echo
            (new Form('filesalias_list'))->action(My::manageUrl())->method('post')->fields([
                (new Text(
                    '',
                    '<div class="table-outer">' .
                    '<table><thead>' .
                    '<caption>' . __('Aliases list') . '</caption>' .
                    '<tr>' .
                    '<th class="nowrap" scope="col">' . __('Destination') . ' - <ins>' . Html::escapeHTML(App::media()->root_url) . '</ins><code>(-?-)</code></th>' .
                    '<th class="nowrap" scope="col">' . __('Alias') . ' - <ins>' . App::blog()->url() . App::url()->getBase('filesalias') . '/' . '</ins><code>(-?-)</code></th>' .
                    '<th class="nowrap" scope="col">' . __('Password') . '</th>' .
                    '<th class="nowrap" scope="col">' . __('Disposable') . '</th>' .
                    '</tr></thead><body>' .
                    $lines .
                    '</tobdy></table></div>'
                )),
                (new Para())->items([
                    (new Submit(['save']))->value(__('Update')),
                    ... My::hiddenFields(['part' => 'list']),
                ]),
                (new Note())->text(__('To remove a link, empty its alias or destination.'))->class('form-note'),
            ])->render();
        }
    }
}
