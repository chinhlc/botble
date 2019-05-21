<?php

namespace Botble\Contact\Repositories\Eloquent;

use Botble\Contact\Repositories\Interfaces\ContactInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class ContactRepository extends RepositoriesAbstract implements ContactInterface
{
    /**
     * @param array $select
     * @return mixed
     * @author Sang Nguyen
     */
    public function getUnread($select = ['*'])
    {
        $data = $this->model->where('is_read', 0)->select($select)->get();
        $this->resetModel();
        return $data;
    }

    /**
     * @return int
     * @author Sang Nguyen
     */
    public function countUnread()
    {
        $data = $this->model->where('is_read', 0)->count();
        $this->resetModel();
        return $data;
    }
}
