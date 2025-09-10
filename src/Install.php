<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

/**
 * @brief       filesAlias installation class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Install
{
    use TraitProcess;

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
            $s = App::db()->structure();

            $s->table(My::ALIAS_TABLE_NAME)
                ->field('blog_id', 'varchar', 32, false)
                ->field('filesalias_url', 'varchar', 255, false)
                ->field('filesalias_destination', 'varchar', 255, false)
                ->field('filesalias_password', 'varchar', 32, true, null)
                ->field('filesalias_disposable', 'smallint', 0, false, 0)

                ->primary('pk_filesalias', 'blog_id', 'filesalias_url')
                ->index('idx_filesalias_blog_id', 'btree', 'blog_id')
                ->reference('fk_filesalias_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade')
            ;

            App::db()->structure()->synchronize($s);

            return true;
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
