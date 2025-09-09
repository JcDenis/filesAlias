<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;

/**
 * @brief       filesAlias prepend class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Prepend
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::PREPEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // register file alias frontend URL handler
        App::url()->register(
            'filesalias',
            'pub',
            '^pub/(.+)$',
            UrlHandler::alias(...)
        );

        return true;
    }
}
