<?php

namespace Botble\ACL\Http\Controllers;

use Botble\ACL\Forms\RoleForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Assets;
use Auth;
use Botble\ACL\Events\RoleAssignmentEvent;
use Botble\ACL\Events\RoleUpdateEvent;
use Botble\ACL\Tables\RoleTable;
use Botble\ACL\Http\Requests\RoleCreateRequest;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\RoleUserInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * @var RoleInterface
     */
    protected $roleRepository;

    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @var RoleUserInterface
     */
    protected $roleUserRepository;

    /**
     * RoleController constructor.
     * @param RoleInterface $roleRepository
     * @param UserInterface $userRepository
     * @param RoleUserInterface $roleUserRepository
     */
    public function __construct(
        RoleInterface $roleRepository,
        UserInterface $userRepository,
        RoleUserInterface $roleUserRepository
    )
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->roleUserRepository = $roleUserRepository;
    }


    /**
     * Show list roles
     *
     * @param RoleTable $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getList(RoleTable $dataTable)
    {
        page_title()->setTitle(trans('core.acl::permissions.role_permission'));

        return $dataTable->renderTable();
    }

    /**
     * Delete a role
     *
     * @param int $id
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function getDelete($id, BaseHttpResponse $response)
    {
        $role = $this->roleRepository->findOrFail($id);

        $role->delete();
        return $response->setMessage(trans('core.acl::permissions.delete_success'));
    }

    /**
     * Delete many roles
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postDeleteMany(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core.base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $role = $this->roleRepository->findOrFail($id);
            $role->delete();
        }
        return $response->setMessage(trans('core.base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     * @author Sang Nguyen
     */
    public function getEdit($id, FormBuilder $formBuilder)
    {
        $role = $this->roleRepository->findOrFail($id);

        Assets::addStylesheets(['jquery-ui', 'jqueryTree']);
        Assets::addJavascript(['jquery-ui', 'jqueryTree']);
        Assets::addAppModule(['role']);

        page_title()->setTitle(trans('core.acl::permissions.details') . ' - ' . e($role->name));

        return $formBuilder->create(RoleForm::class)->setModel($role)->renderForm();
    }

    /**
     * @param int $id
     * @param RoleCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postEdit($id, RoleCreateRequest $request, BaseHttpResponse $response)
    {
        $role = $this->roleRepository->findOrFail($id);

        $role->name = $request->input('name');
        $role->permissions = $this->cleanPermission($request->input('flags'));
        $role->description = $request->input('description');
        $role->updated_by = Auth::user()->getKey();
        $role->is_default = $request->input('is_default', 0);
        $this->roleRepository->createOrUpdate($role);

        event(new RoleUpdateEvent($role));

        return $response
            ->setPreviousUrl(route('roles.list'))
            ->setNextUrl(route('roles.edit', $id))
            ->setMessage(trans('core.acl::permissions.modified_success'));
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getCreate(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('core.acl::permissions.create_role'));

        Assets::addStylesheets(['jquery-ui', 'jqueryTree']);
        Assets::addJavascript(['jquery-ui', 'jqueryTree']);
        Assets::addAppModule(['role']);

        return $formBuilder->create(RoleForm::class)->renderForm();
    }

    /**
     * @param RoleCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postCreate(RoleCreateRequest $request, BaseHttpResponse $response)
    {
        $role = $this->roleRepository->create([
            'name' => $request->input('name'),
            'slug' => str_slug($request->input('name')),
            'permissions' => $this->cleanPermission($request->input('flags')),
            'description' => $request->input('description'),
            'is_default' => $request->input('is_default') !== null ? 1 : 0,
            'created_by' => Auth::user()->getKey(),
            'updated_by' => Auth::user()->getKey(),
        ]);

        return $response
            ->setPreviousUrl(route('roles.list'))
            ->setNextUrl(route('roles.edit', $role->id))
            ->setMessage(trans('core.acl::permissions.create_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function getDuplicate($id, BaseHttpResponse $response)
    {
        $baseRole = $this->roleRepository->findOrFail($id);

        $role = $this->roleRepository->createOrUpdate([
            'name' => $baseRole->name . ' (Duplicate)',
            'slug' => $this->roleRepository->createSlug($baseRole->slug, 0),
            'permissions' => $baseRole->permissions,
            'description' => $baseRole->description,
            'created_by' => $baseRole->created_by,
            'updated_by' => $baseRole->updated_by,
        ]);

        return $response
            ->setPreviousUrl(route('roles.edit', $baseRole->id))
            ->setNextUrl(route('roles.edit', $role->id))
            ->setMessage(trans('core.acl::permissions.duplicated_success'));
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getJson()
    {
        $pl = [];
        foreach ($this->roleRepository->all() as $role) {
            $pl[] = [
                'value' => $role->id,
                'text' => $role->name,
            ];
        }

        return $pl;
    }

    /**
     * @param Request $request
     * @author Sang Nguyen
     */
    public function postAssignMember(Request $request)
    {
        $user = $this->userRepository->findOrFail($request->input('pk'));
        $role = $this->roleRepository->findOrFail($request->input('value'));
        $this->roleUserRepository->deleteBy(['user_id' => $user->id]);

        $this->roleUserRepository->createOrUpdate([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        event(new RoleAssignmentEvent($role, $user));
    }

    /**
     * Return a correctly type casted permissions array
     * @param array $permissions
     * @return array
     * @author Sang Nguyen
     */
    protected function cleanPermission($permissions)
    {
        if (!$permissions) {
            return [];
        }
        $cleanedPermissions = [];
        foreach ($permissions as $permissionName) {
            $cleanedPermissions[$permissionName] = true;
        }

        return $cleanedPermissions;
    }
}
