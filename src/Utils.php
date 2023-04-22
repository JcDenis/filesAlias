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
use dcRecord;
use Exception;

class Utils
{
    public static function getAliases(): dcRecord
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        return new dcRecord(dcCore::app()->con->select(
            'SELECT filesalias_url, filesalias_destination, filesalias_password, filesalias_disposable ' .
            'FROM ' . dcCore::app()->prefix . My::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escapeStr($blog_id) . "' " .
            'ORDER BY filesalias_url ASC '
        ));
    }

    public static function getAlias(string $url): dcRecord
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        return new dcRecord(dcCore::app()->con->select(
            'SELECT filesalias_url, filesalias_destination, filesalias_password, filesalias_disposable ' .
            'FROM ' . dcCore::app()->prefix . My::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escapeStr($blog_id) . "' " .
            "AND filesalias_url = '" . dcCore::app()->con->escapeStr($url) . "' " .
            'ORDER BY filesalias_url ASC '
        ));
    }

    public static function updateAliases(array $aliases): void
    {
        dcCore::app()->con->begin();

        try {
            self::deleteAliases();
            foreach ($aliases as $k => $v) {
                if (!empty($v['filesalias_url']) && !empty($v['filesalias_destination'])) {
                    $v['filesalias_disposable'] = isset($v['filesalias_disposable']) ? true : false;
                    self::createAlias($v['filesalias_url'], $v['filesalias_destination'], $v['filesalias_disposable'], $v['filesalias_password']);
                }
            }

            dcCore::app()->con->commit();
        } catch (Exception $e) {
            dcCore::app()->con->rollback();

            throw $e;
        }
    }

    public static function createAlias(string $url, string $destination, bool $disposable = false, ?string $password = null): void
    {
        if (!$url) {
            throw new Exception(__('File URL is empty.'));
        }

        if (!$destination) {
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

    public static function deleteAliases(): void
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        dcCore::app()->con->execute(
            'DELETE FROM ' . dcCore::app()->prefix . My::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escapeStr($blog_id) . "' "
        );
    }

    public static function deleteAlias(string $url): void
    {
        // nullsafe
        $blog_id = is_null(dcCore::app()->blog) ? '' : dcCore::app()->blog->id;

        dcCore::app()->con->execute(
            'DELETE FROM ' . dcCore::app()->prefix . My::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escapeStr($blog_id) . "' " .
            "AND filesalias_url = '" . dcCore::app()->con->escapeStr($url) . "' "
        );
    }

    public static function getMediaId(string $target): int
    {
        // nullsafe
        if (is_null(dcCore::app()->blog)) {
            return 0;
        }

        $strReq = 'SELECT media_id ' .
        'FROM ' . dcCore::app()->prefix . dcMedia::MEDIA_TABLE_NAME . ' ' .
        "WHERE media_path = '" . dcCore::app()->con->escapeStr((string) dcCore::app()->blog->settings->get('system')->get('public_path')) . "' " .
        "AND media_file = '" . dcCore::app()->con->escapeStr($target) . "' ";

        $rs = dcCore::app()->con->select($strReq);

        return $rs->count() ? (int) $rs->f('media_id') : 0;
    }
}
