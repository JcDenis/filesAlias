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
use Dotclear\Database\MetaRecord;
use Dotclear\Database\Statement\{
    DeleteStatement,
    SelectStatement
};
use Exception;

/**
 * fileAlias records utils
 */
class Utils
{
    /**
     * Get aliases records.
     *
     * @return  MetaRecord    The file alias records
     */
    public static function getAliases(): MetaRecord
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        $sql = new SelectStatement();
        $rs  = $sql->from(dcCore::app()->prefix . My::ALIAS_TABLE_NAME)
            ->columns([
                'filesalias_url',
                'filesalias_destination',
                'filesalias_password',
                'filesalias_disposable',
            ])
            ->where('blog_id = ' . $sql->quote($blog_id))
            ->order('filesalias_url ASC')
            ->select();

        return is_null($rs) ? MetaRecord::newFromArray([]) : $rs;
    }

    /**
     * Get alias record.
     *
     * @return  MetaRecord    The alias record
     */
    public static function getAlias(string $url): MetaRecord
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        $sql = new SelectStatement();
        $rs  = $sql->from(dcCore::app()->prefix . My::ALIAS_TABLE_NAME)
            ->columns([
                'filesalias_url',
                'filesalias_destination',
                'filesalias_password',
                'filesalias_disposable',
            ])
            ->where('blog_id = ' . $sql->quote($blog_id))
            ->and('filesalias_url = ' . $sql->quote($url))
            ->order('filesalias_url ASC')
            ->select();

        return is_null($rs) ? MetaRecord::newFromArray([]) : $rs;
    }

    /**
     * Update aliases.
     *
     * This remove all aliases on current blog
     * before creating new ones.
     *
     * Each $aliases entry looks like:
     * [
     *      filesalias_url => string,
     *      filesalias_destination => string,
     *      filesalias_disposable => bool
     *      filesalias_password => string
     * ]
     *
     * @param   array{filesalias_url:string,filesalias_destination:string,filesalias_disposable:bool,filesalias_password:string}    $aliases    The new aliases
     */
    public static function updateAliases(array $aliases): void
    {
        dcCore::app()->con->begin();

        try {
            self::deleteAliases();
            foreach ($aliases as $k => $v) {
                if (!empty($v['filesalias_url']) && !empty($v['filesalias_destination'])) {
                    $v['filesalias_disposable'] = !empty($v['filesalias_disposable']);
                    self::createAlias($v['filesalias_url'], $v['filesalias_destination'], $v['filesalias_disposable'], $v['filesalias_password']);
                }
            }

            dcCore::app()->con->commit();
        } catch (Exception $e) {
            dcCore::app()->con->rollback();

            throw $e;
        }
    }

    /**
     * Create an alias.
     *
     * @param   string          $url            The URL
     * @param   string          $destination    The destination
     * @param   bool            $disposable     Is disposable
     * @param   null|string     $password       The optionnal password
     */
    public static function createAlias(string $url, string $destination, bool $disposable = false, ?string $password = null): void
    {
        if (empty($url)) {
            throw new Exception(__('File URL is empty.'));
        }

        if (empty($destination)) {
            throw new Exception(__('File destination is empty.'));
        }

        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . My::ALIAS_TABLE_NAME);
        $cur->setField('blog_id', $blog_id);
        $cur->setField('filesalias_url', (string) $url);
        $cur->setField('filesalias_destination', (string) $destination);
        $cur->setField('filesalias_password', $password);
        $cur->setField('filesalias_disposable', (int) $disposable);
        $cur->insert();
    }

    /**
     * Delete all aliases.
     */
    public static function deleteAliases(): void
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        $sql = new DeleteStatement();
        $sql->from(dcCore::app()->prefix . My::ALIAS_TABLE_NAME)
            ->where('blog_id = ' . $sql->quote($blog_id))
            ->delete();
    }

    /**
     * Dlete an alias.
     *
     * @param   string  $url    The alias URL
     */
    public static function deleteAlias(string $url): void
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        $sql = new DeleteStatement();
        $sql->from(dcCore::app()->prefix . My::ALIAS_TABLE_NAME)
            ->where('blog_id = ' . $sql->quote($blog_id))
            ->and('filesalias_url = ' . $sql->quote($url))
            ->delete();
    }

    /**
     * Get media id.
     *
     * @param   string  $target     The media file name
     *
     * @return  int     The media ID
     */
    public static function getMediaId(string $target): int
    {
        // nullsafe
        if (is_null(dcCore::app()->blog)) {
            return 0;
        }
        $path = dcCore::app()->blog->settings->get('system')->get('public_path');

        $sql = new SelectStatement();
        $rs  = $sql->from(dcCore::app()->prefix . dcMedia::MEDIA_TABLE_NAME)
            ->column('media_id')
            ->where('media_path = ' . $sql->quote(is_string($path) ? $path : ''))
            ->and('media_file = ' . $sql->quote($target))
            ->select();

        return $rs->count() ? (int) $rs->f('media_id') : 0;
    }
}
