<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Database\MetaRecord;
use Dotclear\Database\Statement\DeleteStatement;
use Dotclear\Database\Statement\SelectStatement;
use Exception;

/**
 * @brief       filesAlias records helper class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
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
        $sql = new SelectStatement();
        $rs  = $sql->from(App::db()->con()->prefix() . My::ALIAS_TABLE_NAME)
            ->columns([
                'filesalias_url',
                'filesalias_destination',
                'filesalias_password',
                'filesalias_disposable',
            ])
            ->where('blog_id = ' . $sql->quote(App::blog()->id()))
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
        $sql = new SelectStatement();
        $rs  = $sql->from(App::db()->con()->prefix() . My::ALIAS_TABLE_NAME)
            ->columns([
                'filesalias_url',
                'filesalias_destination',
                'filesalias_password',
                'filesalias_disposable',
            ])
            ->where('blog_id = ' . $sql->quote(App::blog()->id()))
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
     * @param   AliasRow[]    $aliases    The new aliases
     */
    public static function updateAliases(array $aliases): void
    {
        App::db()->con()->begin();

        try {
            self::deleteAliases();
            foreach ($aliases as $row) {
                if (is_a($row, AliasRow::class)) { // @phpstan-ignore-line
                    self::createAlias($row);
                }
            }

            App::db()->con()->commit();
        } catch (Exception $e) {
            App::db()->con()->rollback();

            throw $e;
        }
    }

    /**
     * Create an alias.
     *
     * @param   AliasRow    $alias  The alias representation 
     */
    public static function createAlias(AliasRow $alias): void
    {
        if ($alias->url === '') {
            throw new Exception(__('File URL is empty.'));
        }

        if ($alias->destination === '') {
            throw new Exception(__('File destination is empty.'));
        }

        $cur = App::db()->con()->openCursor(App::db()->con()->prefix() . My::ALIAS_TABLE_NAME);
        $cur->setField('blog_id', App::blog()->id());
        $cur->setField('filesalias_url', $alias->url);
        $cur->setField('filesalias_destination', $alias->destination);
        $cur->setField('filesalias_password', $alias->password);
        $cur->setField('filesalias_disposable', (int) $alias->disposable);
        $cur->insert();
    }

    /**
     * Delete all aliases.
     */
    public static function deleteAliases(): void
    {
        $sql = new DeleteStatement();
        $sql->from(App::db()->con()->prefix() . My::ALIAS_TABLE_NAME)
            ->where('blog_id = ' . $sql->quote(App::blog()->id()))
            ->delete();
    }

    /**
     * Dlete an alias.
     *
     * @param   string  $url    The alias URL
     */
    public static function deleteAlias(string $url): void
    {
        $sql = new DeleteStatement();
        $sql->from(App::db()->con()->prefix() . My::ALIAS_TABLE_NAME)
            ->where('blog_id = ' . $sql->quote(App::blog()->id()))
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
        if (!App::blog()->isDefined()) {
            return 0;
        }
        $path = App::blog()->settings()->get('system')->get('public_path');

        $sql = new SelectStatement();
        $rs  = $sql->from(App::db()->con()->prefix() . App::postMedia()::MEDIA_TABLE_NAME)
            ->column('media_id')
            ->where('media_path = ' . $sql->quote(is_string($path) ? $path : ''))
            ->and('media_file = ' . $sql->quote($target))
            ->select();

        return !is_null($rs) && $rs->count() ? (int) $rs->f('media_id') : 0;
    }
}
