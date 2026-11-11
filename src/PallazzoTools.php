<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

/**
 * @brief       filesAlias randomizer class.
 * @ingroup     filesAlias
 *
 * Adapted from Enrico Pallazzo class.
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class PallazzoTools
{
    /**
     * Get a random alias.
     *
     * Uses a cryptographically secure generator, as alias may be the only protection of a file.
     */
    public static function rand_uniqid(): string
    {
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $out   = '';
        for ($n = 0; $n < 12; $n++) {
            $out .= $index[random_int(0, strlen($index) - 1)];
        }

        return $out;
    }
}
