<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\Module\MyPlugin;

/**
 * @brief       filesAlias My helper.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class My extends MyPlugin
{
    /**
     * This plugin table name.
     *
     * @var     string  ALIAS_TABLE_NAME
     */
    public const ALIAS_TABLE_NAME = 'filesalias';

    // Use defautl permissions
}
