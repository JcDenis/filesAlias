<?php
/**
 * @file
 * @brief       The plugin filesAlias definition
 * @ingroup     filesAlias
 *
 * @defgroup    filesAlias Plugin filesAlias.
 *
 * Manage aliases of your blog's media.
 *
 * @author      Osku (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

$this->registerModule(
    'Files alias',
    "Manage aliases of your blog's media",
    'Osku and contributors',
    '1.3',
    [
        'requires'    => [['core', '2.36']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-09-09T15:58:45+00:00',
    ]
);
