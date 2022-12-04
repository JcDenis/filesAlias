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
if (!defined('DC_RC_PATH')) {
    return null;
}

dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/default-templates');
dcCore::app()->tpl->addValue('fileAliasURL', ['templateAlias','fileAliasURL']);

class templateAlias
{
    public static function fileAliasURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->url.dcCore::app()->url->getBase("filesalias")."/".dcCore::app()->ctx->filealias->filesalias_url') . '; ?>';
    }
}

class urlFilesAlias extends dcUrlHandlers
{
    public static function alias($args)
    {
        $delete = false;

        dcCore::app()->ctx->filealias = dcCore::app()->filealias->getAlias($args);

        if (dcCore::app()->ctx->filealias->isEmpty()) {
            self::p404();
        }

        if (dcCore::app()->ctx->filealias->filesalias_disposable) {
            $delete = true;
        }

        if (dcCore::app()->ctx->filealias->filesalias_password) {
            # Check for match
            if (!empty($_POST['filepassword']) && $_POST['filepassword'] == dcCore::app()->ctx->filealias->filesalias_password) {
                self::servefile(dcCore::app()->ctx->filealias->filesalias_destination, $args, $delete);
            } else {
                self::serveDocument('file-password-form.html', 'text/html', false);

                return;
            }
        } else {
            self::servefile(dcCore::app()->ctx->filealias->filesalias_destination, $args, $delete);
        }
    }

    public static function servefile($target, $alias, $delete = false)
    {
        $a     = new aliasMedia();
        $media = $a->getMediaId($target);

        if (empty($media)) {
            self::p404();
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
            dcCore::app()->filealias->deleteAlias($alias);
        }
    }
}
