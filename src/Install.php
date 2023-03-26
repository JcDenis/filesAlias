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

use dbStruct;
use dcCore;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_CONTEXT_ADMIN') && dcCore::app()->newVersion(My::id(), dcCore::app()->plugins->moduleInfo(My::id(), 'version'));

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        try {
            $s = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);

            $s->{My::ALIAS_TABLE_NAME}
                ->blog_id('varchar', 32, false)
                ->filesalias_url('varchar', 255, false)
                ->filesalias_destination('varchar', 255, false)
                ->filesalias_password('varchar', 32, true, null)
                ->filesalias_disposable('smallint', 0, false, 0)

                ->primary('pk_filesalias', 'blog_id', 'filesalias_url')
                ->index('idx_filesalias_blog_id', 'btree', 'blog_id')
                ->reference('fk_filesalias_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade')
            ;

            $si      = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);
            $changes = $si->synchronize($s);

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}