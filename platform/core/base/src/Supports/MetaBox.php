<?php

namespace Botble\Base\Supports;

use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class MetaBox
{
    /**
     * @var array
     */
    protected $metaBoxes = [];

    /**
     * @var MetaBoxInterface
     */
    protected $metaBoxRepository;

    /**
     * MetaBox constructor.
     * @param MetaBoxInterface $metaBoxRepository
     */
    public function __construct(MetaBoxInterface $metaBoxRepository)
    {
        $this->metaBoxRepository = $metaBoxRepository;
    }

    /**
     * @param $id
     * @param $title
     * @param $callback
     * @param null $screen
     * @param string $context
     * @param string $priority
     * @param null $callback_args
     */
    public function addMetaBox(
        $id,
        $title,
        $callback,
        $screen = null,
        $context = 'advanced',
        $priority = 'default',
        $callback_args = null
    ) {
        if (!isset($this->metaBoxes[$screen])) {
            $this->metaBoxes[$screen] = [];
        }
        if (!isset($this->metaBoxes[$screen][$context])) {
            $this->metaBoxes[$screen][$context] = [];
        }

        foreach (array_keys($this->metaBoxes[$screen]) as $a_context) {
            foreach (['high', 'core', 'default', 'low'] as $a_priority) {
                if (!isset($this->metaBoxes[$screen][$a_context][$a_priority][$id])) {
                    continue;
                }

                // If a core box was previously added or removed by a plugin, don't add.
                if ('core' == $priority) {
                    // If core box previously deleted, don't add
                    if (false === $this->metaBoxes[$screen][$a_context][$a_priority][$id]) {
                        return;
                    }

                    /*
                     * If box was added with default priority, give it core priority to
                     * maintain sort order.
                     */
                    if ('default' == $a_priority) {
                        $this->metaBoxes[$screen][$a_context]['core'][$id] = $this->metaBoxes[$screen][$a_context]['default'][$id];
                        unset($this->metaBoxes[$screen][$a_context]['default'][$id]);
                    }
                    return;
                }
                /* If no priority given and id already present, use existing priority.
                 *
                 * Else, if we're adding to the sorted priority, we don't know the title
                 * or callback. Grab them from the previously added context/priority.
                 */
                if (empty($priority)) {
                    $priority = $a_priority;
                } elseif ('sorted' == $priority) {
                    $title = $this->metaBoxes[$screen][$a_context][$a_priority][$id]['title'];
                    $callback = $this->metaBoxes[$screen][$a_context][$a_priority][$id]['callback'];
                    $callback_args = $this->metaBoxes[$screen][$a_context][$a_priority][$id]['args'];
                }
                // An id can be in only one priority and one context.
                if ($priority != $a_priority || $context != $a_context) {
                    unset($this->metaBoxes[$screen][$a_context][$a_priority][$id]);
                }
            }
        }

        if (empty($priority)) {
            $priority = 'low';
        }

        if (!isset($this->metaBoxes[$screen][$context][$priority])) {
            $this->metaBoxes[$screen][$context][$priority] = [];
        }

        $this->metaBoxes[$screen][$context][$priority][$id] = [
            'id'       => $id,
            'title'    => $title,
            'callback' => $callback,
            'args'     => $callback_args,
        ];
    }

    /**
     * Meta-Box template function
     *
     * @param string $screen Screen identifier
     * @param string $context box context
     * @param mixed $object gets passed to the box callback function as first parameter
     * @return int number of metaBoxes
     *
     * @throws Throwable
     */
    public function doMetaBoxes($screen, $context, $object = null)
    {
        $index = 0;
        $data = '';
        if (isset($this->metaBoxes[$screen][$context])) {
            foreach (['high', 'sorted', 'core', 'default', 'low'] as $priority) {
                if (!isset($this->metaBoxes[$screen][$context][$priority])) {
                    continue;
                }

                foreach ((array)$this->metaBoxes[$screen][$context][$priority] as $box) {
                    if (false == $box || !$box['title']) {
                        continue;
                    }
                    $index++;
                    $data .= view('core/base::elements.forms.meta-box-wrap', [
                        'box'      => $box,
                        'callback' => call_user_func_array($box['callback'], [$object, $screen, $box]),
                    ])->render();
                }
            }
        }

        echo view('core/base::elements.forms.meta-box', compact('data', 'context'))->render();

        return $index;
    }

    /**
     * Remove a meta box from an edit form.
     *
     * @param string $id String for use in the 'id' attribute of tags.
     * @param string|object $screen The screen on which to show the box (post, page, link).
     * @param string $context The context within the page where the boxes should show ('normal', 'advanced').
     */
    public function removeMetaBox($id, $screen, $context)
    {
        if (!isset($this->metaBoxes[$screen])) {
            $this->metaBoxes[$screen] = [];
        }

        if (!isset($this->metaBoxes[$screen][$context])) {
            $this->metaBoxes[$screen][$context] = [];
        }

        foreach (['high', 'core', 'default', 'low'] as $priority) {
            $this->metaBoxes[$screen][$context][$priority][$id] = false;
        }
    }

    /**
     * @param Model $object
     * @param $key
     * @param $value
     * @param $options
     * @return boolean
     * @throws Exception
     */
    public function saveMetaBoxData($object, $key, $value, $options = null)
    {
        try {
            $fieldMeta = $this->metaBoxRepository->getFirstBy([
                'meta_key'       => $key,
                'reference_id'   => $object->id,
                'reference_type' => get_class($object),
            ]);
            if (!$fieldMeta) {
                $fieldMeta = $this->metaBoxRepository->getModel();
                $fieldMeta->reference_id = $object->id;
                $fieldMeta->meta_key = $key;
                $fieldMeta->reference_type = get_class($object);
            }

            if (!empty($options)) {
                $fieldMeta->options = $options;
            }

            $fieldMeta->meta_value = [$value];
            $this->metaBoxRepository->createOrUpdate($fieldMeta);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * @param Model $object
     * @param $key
     * @param boolean $single
     * @param array $select
     * @return mixed
     */
    public function getMetaData($object, $key, $single = false, $select = ['meta_value'])
    {
        $field = $this->getMeta($object, $key, $select);
        if (!$field) {
            return $single ? '' : [];
        }

        if ($single) {
            return $field->meta_value[0];
        }
        return $field->meta_value;
    }

    /**
     * @param Model $object
     * @param $key
     * @param array $select
     * @return mixed
     */
    public function getMeta($object, $key, $select = ['meta_value'])
    {
        return $this->metaBoxRepository->getFirstBy([
            'meta_key'       => $key,
            'reference_id'   => $object->id,
            'reference_type' => get_class($object),
        ], $select);
    }

    /**
     * @param Model $object
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function deleteMetaData($object, $key)
    {
        return $this->metaBoxRepository->deleteBy([
            'meta_key'       => $key,
            'reference_id'   => $object->id,
            'reference_type' => get_class($object),
        ]);
    }
}
