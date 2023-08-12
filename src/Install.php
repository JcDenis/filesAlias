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
use Dotclear\Core\Process;
use Dotclear\Database\Structure;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            $s = new Structure(dcCore::app()->con, dcCore::app()->prefix);

            $s->__get(My::ALIAS_TABLE_NAME)
                ->field('blog_id', 'varchar', 32, false)
                ->field('filesalias_url', 'varchar', 255, false)
                ->field('filesalias_destination', 'varchar', 255, false)
                ->field('filesalias_password', 'varchar', 32, true, null)
                ->field('filesalias_disposable', 'smallint', 0, false, 0)

                ->primary('pk_filesalias', 'blog_id', 'filesalias_url')
                ->index('idx_filesalias_blog_id', 'btree', 'blog_id')
                ->reference('fk_filesalias_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade')
            ;

            (new Structure(dcCore::app()->con, dcCore::app()->prefix))->synchronize($s);

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
