<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\App;
use Dotclear\Core\Process;

/**
 * @brief       filesAlias frontend class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // add path to template
        App::frontend()->template()->appendPath(
            My::path() . DIRECTORY_SEPARATOR . 'default-templates'
        );
        // register template value for file alias
        App::frontend()->template()->addValue(
            'fileAliasURL',
            FrontendTemplate::fileAliasURL(...)
        );

        return true;
    }
}
