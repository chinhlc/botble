<?php

namespace Botble\Media\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface MediaFolderInterface extends RepositoryInterface
{

    /**
     * @param $folderId
     * @param array $params
     * @param bool $withTrash
     * @return mixed
     */
    public function getFolderByParentId($folderId, array $params = [], $withTrash = false);

    /**
     * @param $name
     * @param $parentId
     * @return string
     */
    public function createSlug($name, $parentId);

    /**
     * @param $name
     * @param $parentId
     */
    public function createName($name, $parentId);

    /**
     * @param $parentId
     * @param array $breadcrumbs
     * @return array
     */
    public function getBreadcrumbs($parentId, $breadcrumbs = []);

    /**
     * @param $parentId
     * @param array $params
     * @return mixed
     */
    public function getTrashed($parentId, array $params = []);

    /**
     * @param $folderId
     * @param bool $force
     */
    public function deleteFolder($folderId, $force = false);

    /**
     * @param $parentId
     * @param array $child
     * @return array
     */
    public function getAllChildFolders($parentId, $child = []);

    /**
     * @param $folderId
     * @param string $path
     * @return string
     */
    public function getFullPath($folderId, $path = '');

    /**
     * @param $folderId
     */
    public function restoreFolder($folderId);

    /**
     * @return mixed
     */
    public function emptyTrash();
}
