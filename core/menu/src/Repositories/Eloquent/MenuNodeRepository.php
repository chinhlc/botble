<?php

namespace Botble\Menu\Repositories\Eloquent;

use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Query\JoinClause;

class MenuNodeRepository extends RepositoriesAbstract implements MenuNodeInterface
{
    /**
     * @param $menu_id
     * @param $parent_id
     * @param array $selects
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     * @author Sang Nguyen
     */
    public function getByMenuId($menu_id, $parent_id, $select = ['*'])
    {
        $data = $this->model
            ->with(['child'])
            ->where([
                'menu_id' => $menu_id,
                'parent_id' => $parent_id,
            ])
            ->leftJoin('slugs', function (JoinClause $join) {
                $join->on('slugs.reference_id', '=', 'menu_nodes.related_id');
                $join->on('slugs.reference', '=', 'menu_nodes.type');
            })
            ->select($select)
            ->orderBy('position', 'asc')
            ->get();
        $this->resetModel();
        return $data;
    }
}
