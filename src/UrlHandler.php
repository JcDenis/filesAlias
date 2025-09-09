<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Core\Url;

/**
 * @brief       filesAlias frontend URL handler class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class UrlHandler extends Url
{
    /**
     * File alias page.
     *
     * @param   string  $args   The arguments
     */
    public static function alias(string $args): void
    {
        $alias = Utils::getAlias($args);

        App::frontend()->context()->__set('filealias', $alias);

        if ($alias->isEmpty()) {
            self::p404();
        }

        $disposable  = !empty($alias->f('filesalias_disposable'));
        $password    = is_string($alias->f('filesalias_password')) ? $alias->f('filesalias_password') : '';
        $destination = is_string($alias->f('filesalias_destination')) ? $alias->f('filesalias_destination') : '';

        if ($password) {
            # Check for match
            if (!empty($_POST['filepassword']) && $_POST['filepassword'] == $password) {
                self::servefile($destination, $args, $disposable);
            } else {
                self::serveDocument('file-password-form.html', 'text/html', false);

                return;
            }
        } else {
            self::servefile($destination, $args, $disposable);
        }
    }

    /**
     * File alias frontend file server.
     *
     * @param   string  $target     The media file name
     * @param   string  $alias      The alias
     * @param   bool    $delete     Delete after serve
     */
    private static function servefile(string $target, string $alias, bool $delete = false): void
    {
        $media = Utils::getMediaId($target);

        if (empty($media)) {
            self::p404();
        }

        $file = App::media()->getFile($media);

        if (empty($file->file)) {
            self::p404();
        }

        header('Content-type: ' . $file->type);
        header('Content-Length: ' . $file->size);
        header('Content-Disposition: attachment; filename="' . $file->basename . '"');

        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        flush();

        readfile($file->file);
        if ($delete) {
            Utils::deleteAlias($alias);
        }
    }
}
