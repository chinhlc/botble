<?php

namespace Botble\Menu\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface MenuNodeInterface extends RepositoryInterface
{
    /**
     * @param $parent_id
     * @param null array
     * @return array|Collection|static[]
     */
    public function getByMenuId($menu_id, $parent_id, $select = ['*']);
}
