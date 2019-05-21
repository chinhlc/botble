<?php

namespace Botble\ACL\Forms;

use Botble\ACL\Http\Requests\RoleCreateRequest;
use Botble\Base\Forms\FormAbstract;

class RoleForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        $flags = $this->getAvailablePermissions();
        $children = $this->getPermissionTree($flags);
        $active = [];

        if ($this->getModel()) {
            $active = array_keys($this->getModel()->permissions);
        }

        $this
            ->setModuleName(ROLE_MODULE_SCREEN_NAME)
            ->setValidatorClass(RoleCreateRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core.base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core.base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core.base::forms.description'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core.base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('is_default', 'checkbox', [
                'label' => trans('core.base::forms.is_default'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'hrv-checkbox',
                ],
                'value' => 1,
            ])
            ->addMetaBoxes([
                'permissions' => [
                    'title' => trans('core.acl::permissions.permission_flags'),
                    'content' => view('core.acl::roles.permissions', compact('active', 'flags', 'children'))->render(),
                ],
            ])
            ->setActionButtons(view('core.acl::roles.actions', ['role' => $this->getModel()])->render());
    }

    /**
     * @param int $parentId
     * @param array $allFlags
     * @return mixed
     * @author Sang Nguyen
     */
    protected function getChildren($parentId, $allFlags)
    {
        $newFlagArray = [];
        foreach ($allFlags as $flagDetails) {
            if (array_get($flagDetails, 'parent_flag', 'root') == $parentId) {
                $newFlagArray[] = $flagDetails['flag'];
            }
        }
        return $newFlagArray;
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    protected function getAvailablePermissions(): array
    {
        $flags = [];

        $configuration = config(strtolower('cms-permissions'));
        if (!empty($configuration)) {
            foreach ($configuration as $config) {
                $flags[$config['flag']] = $config;
            }
        }

        foreach (scan_folder(base_path() . '/core') as $module) {
            $configuration = config(strtolower('core.' . $module . '.permissions'));
            if (!empty($configuration)) {
                foreach ($configuration as $config) {
                    $flags[$config['flag']] = $config;
                }
            }
        }

        foreach (get_active_plugins() as $plugin) {
            $configuration = config(strtolower('plugins.' . $plugin . '.permissions'));
            if ($configuration) {
                foreach ($configuration as $config) {
                    $flags[$config['flag']] = $config;
                }
            }
        }

        return $flags;
    }

    /**
     * @param array $flags
     * @return array
     * @author Sang Nguyen
     */
    protected function getPermissionTree($flags): array
    {
        $sortedFlag = $flags;
        sort($sortedFlag);
        $children['root'] = $this->getChildren('root', $sortedFlag);

        foreach (array_keys($flags) as $key) {
            $childrenReturned = $this->getChildren($key, $flags);
            if (count($childrenReturned) > 0) {
                $children[$key] = $childrenReturned;
            }
        }

        return $children;
    }
}