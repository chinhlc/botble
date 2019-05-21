<?php

namespace Botble\Base\Supports;

class AdminBar
{
    /**
     * @var array
     */
    protected $groups = [
        'appearance' => [
            'link' => 'javascript:;',
            'title' => 'Appearance',
            'items' => [

            ],
        ],
        'add-new' => [
            'link' => 'javascript:;',
            'title' => 'Add new',
            'items' => [

            ],
        ],
    ];

    /**
     * @var bool
     */
    protected $is_display = true;

    /**
     * @var array
     */
    protected $noGroupLinks = [];

    /**
     * AdminBar constructor.
     */
    public function __construct()
    {
        $this->groups['appearance']['items'] = [];
        $this->groups['add-new']['items'] = [];
    }

    /**
     * @param bool $is_display
     */
    public function setIsDisplay($is_display = true)
    {
        $this->is_display = $is_display;
    }

    /**
     * @return bool
     */
    public function isDisplay()
    {
        return $this->is_display;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getLinksNoGroup()
    {
        return $this->noGroupLinks;
    }

    /**
     * @param $slug
     * @param $title
     * @param string $link
     * @return $this
     */
    public function registerGroup($slug, $title, $link = 'javascript:;')
    {
        if (isset($this->groups[$slug])) {
            $this->groups[$slug]['items'][$title] = $link;
            return $this;
        }
        $this->groups[$slug] = [
            'title' => $title,
            'link' => $link,
            'items' => [

            ],
        ];
        return $this;
    }

    /**
     * @param $title
     * @param $url
     * @param null $group
     * @return $this
     */
    public function registerLink($title, $url, $group = null)
    {
        if ($group === null || !isset($this->groups[$group])) {
            $this->noGroupLinks[] = [
                'link' => $url,
                'title' => $title,
            ];
        } else {
            $this->groups[$group]['items'][$title] = $url;
        }
        return $this;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function render()
    {
        return view('core.base::elements.admin-bar')->render();
    }
}
