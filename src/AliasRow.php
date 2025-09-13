<?php

declare(strict_types=1);

namespace Dotclear\Plugin\filesAlias;

use Dotclear\Database\MetaRecord;

/**
 * @brief       filesAlias backend class.
 * @ingroup     filesAlias
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class AliasRow
{
    public function __construct(
        public readonly string $url = '',
        public readonly string $destination = '',
        public readonly bool $disposable = false,
        public readonly string $password = ''
    ) {
    }

    /**
     * @param   array<string, mixed>    $row
     */
    public static function newFromArray(array $row, string $prefix = ''): self
    {
        return new self(
            url: isset($row[$prefix . 'url']) && is_string($row[$prefix . 'url']) ? $row[$prefix . 'url'] : '',
            destination: isset($row[$prefix . 'destination']) && is_string($row[$prefix . 'destination']) ? $row[$prefix . 'destination'] : '',
            disposable: !empty($row[$prefix . 'disposable']),
            password: isset($row[$prefix . 'password']) && is_string($row[$prefix . 'password']) ? $row[$prefix . 'password'] : ''
        );
    }

    public static function newFromRecord(MetaRecord $rs): self
    {
        return new self(
            url: is_string($rs->f('filesalias_url')) ? $rs->f('filesalias_url') : '',
            destination: is_string($rs->f('filesalias_destination')) ? $rs->f('filesalias_destination') : '',
            disposable: !empty($rs->f('filesalias_disposable')),
            password: is_string($rs->f('filesalias_password')) ? $rs->f('filesalias_password') : ''
        );
    }
}