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
use Dotclear\Plugin\Uninstaller\Uninstaller;

class Uninstall extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::UNINSTALL));
    }

    public static function process(): bool
    {
        if (!self::status() || !dcCore::app()->plugins->moduleExists('Uninstaller')) {
            return false;
        }

        Uninstaller::instance()
            ->addUserAction(
                'tables',
                'delete',
                My::ALIAS_TABLE_NAME
            )
            ->addUserAction(
                'plugins',
                'delete',
                My::id()
            )
            ->addUserAction(
                'versions',
                'delete',
                My::id()
            )
/*
            ->addDirectAction(
                'tables',
                'delete',
                My::ALIAS_TABLE_NAME
            )
            ->addDirectAction(
                'plugins',
                'delete',
                My::id()
            )
            ->addDirectAction(
                'versions',
                'delete',
                My::id()
            )
//*/
        ;

        // no custom action
        return false;
    }
}
