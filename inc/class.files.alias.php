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
class filesAliases
{
    protected $aliases;

    public function __construct()
    {
    }

    public function getAliases()
    {
        if (is_array($this->aliases)) {
            return $this->aliases;
        }

        $this->aliases = [];
        $sql           = 'SELECT filesalias_url, filesalias_destination, filesalias_password, filesalias_disposable ' .
                'FROM ' . dcCore::app()->prefix . initFilesAlias::ALIAS_TABLE_NAME . ' ' .
                "WHERE blog_id = '" . dcCore::app()->con->escape(dcCore::app()->blog->id) . "' " .
                'ORDER BY filesalias_url ASC ';
        $this->aliases = dcCore::app()->con->select($sql)->rows();

        return $this->aliases;
    }

    public function getAlias($url)
    {
        $strReq = 'SELECT filesalias_url, filesalias_destination, filesalias_password, filesalias_disposable ' .
                'FROM ' . dcCore::app()->prefix . initFilesAlias::ALIAS_TABLE_NAME . ' ' .
                "WHERE blog_id = '" . dcCore::app()->con->escape(dcCore::app()->blog->id) . "' " .
                "AND filesalias_url = '" . dcCore::app()->con->escape($url) . "' " .
                'ORDER BY filesalias_url ASC ';

        $rs = dcCore::app()->con->select($strReq);

        return $rs;
    }

    public function updateAliases($aliases)
    {
        dcCore::app()->con->begin();

        try {
            $this->deleteAliases();
            foreach ($aliases as $k => $v) {
                if (!empty($v['filesalias_url']) && !empty($v['filesalias_destination'])) {
                    $v['filesalias_disposable'] = isset($v['filesalias_disposable']) ? true : false;
                    $this->createAlias($v['filesalias_url'], $v['filesalias_destination'], $v['filesalias_disposable'], $v['filesalias_password']);
                }
            }

            dcCore::app()->con->commit();
        } catch (Exception $e) {
            dcCore::app()->con->rollback();

            throw $e;
        }
    }

    public function createAlias($url, $destination, $disposable = 0, $password = null)
    {
        if (!$url) {
            throw new Exception(__('File URL is empty.'));
        }

        if (!$destination) {
            throw new Exception(__('File destination is empty.'));
        }

        $cur                         = dcCore::app()->con->openCursor(dcCore::app()->prefix . initFilesAlias::ALIAS_TABLE_NAME);
        $cur->blog_id                = (string) dcCore::app()->blog->id;
        $cur->filesalias_url         = (string) $url;
        $cur->filesalias_destination = (string) $destination;
        $cur->filesalias_password    = $password;
        $cur->filesalias_disposable  = abs((int) $disposable);
        $cur->insert();
    }

    public function deleteAliases()
    {
        dcCore::app()->con->execute(
            'DELETE FROM ' . dcCore::app()->prefix . initFilesAlias::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escape(dcCore::app()->blog->id) . "' "
        );
    }

    public function deleteAlias($url)
    {
        dcCore::app()->con->execute(
            'DELETE FROM ' . dcCore::app()->prefix . initFilesAlias::ALIAS_TABLE_NAME . ' ' .
            "WHERE blog_id = '" . dcCore::app()->con->escape(dcCore::app()->blog->id) . "' " .
            "AND filesalias_url = '" . dcCore::app()->con->escape($url) . "' "
        );
    }
}

class aliasMedia extends dcMedia
{
    public function __construct()
    {
    }

    public function getMediaId($target)
    {
        $strReq = 'SELECT media_id ' .
        'FROM ' . dcCore::app()->prefix . dcMedia::MEDIA_TABLE_NAME . ' ' .
        //"WHERE media_path = '" . $this->path . "' " .
        "WHERE media_path = '" . dcCore::app()->con->escape(dcCore::app()->blog->settings->system->public_path) . "' " .
        "AND media_file = '" . dcCore::app()->con->escape($target) . "' ";

        $rs = dcCore::app()->con->select($strReq);

        return $rs->count() ? $rs->media_id : null;
    }
}
