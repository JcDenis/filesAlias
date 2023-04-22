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
use dcUrlHandlers;

/**
 * File alias frontend URL handler.
 */
class UrlHandler extends dcUrlHandlers
{
    /**
     * File alias page.
     *
     * @param   string  $args   The arguments
     */
    public static function alias(string $args): void
    {
        // nullsafe
        if (is_null(dcCore::app()->ctx)) {
            return;
        }

        $delete = false;

        dcCore::app()->ctx->__set('filealias', Utils::getAlias($args));

        if (dcCore::app()->ctx->__get('filealias')->isEmpty()) {
            self::p404();
        }

        if (dcCore::app()->ctx->__get('filealias')->f('filesalias_disposable')) {
            $delete = true;
        }

        if (dcCore::app()->ctx->__get('filealias')->f('filesalias_password')) {
            # Check for match
            if (!empty($_POST['filepassword']) && $_POST['filepassword'] == dcCore::app()->ctx->__get('filealias')->f('filesalias_password')) {
                self::servefile(dcCore::app()->ctx->__get('filealias')->f('filesalias_destination'), $args, $delete);
            } else {
                self::serveDocument('file-password-form.html', 'text/html', false);

                return;
            }
        } else {
            self::servefile(dcCore::app()->ctx->__get('filealias')->f('filesalias_destination'), $args, $delete);
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

        if (!(dcCore::app()->media instanceof dcMedia)) {
            dcCore::app()->media = new dcMedia();
        }

        $file = dcCore::app()->media->getFile($media);

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
