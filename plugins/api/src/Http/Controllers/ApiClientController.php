<?php

namespace Botble\Api\Http\Controllers;

use Botble\ACL\Forms\ApiClientForm;
use Botble\Api\Http\Requests\ApiClientRequest;
use Botble\Api\Tables\ApiClientTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Table\TableBuilder;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;

class ApiClientController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function getClients(Request $request, TableBuilder $tableBuilder)
    {
        page_title()->setTitle(trans('plugins.api::api.api_clients'));
        $table = $tableBuilder->create(ApiClientTable::class);

        if ($request->expectsJson()) {
            return $table->renderTable();
        }

        return view('plugins.api::clients', compact('table'));
    }

    /**
     * @return string
     * @author Sang Nguyen
     */
    public function getCreateClient(FormBuilder $builder)
    {
        /**
         * @var ApiClientForm $form
         */
        $form = $builder->create(ApiClientForm::class, ['url' => route('api.clients.create.post')]);
        $form->setTitle(trans('plugins.api::api.create_new_client'));
        return $form->setUseInlineJs(true)->renderForm();
    }

    /**
     * @param ApiClientRequest $request
     * @param ClientRepository $clientRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postCreateClient(
        ApiClientRequest $request,
        ClientRepository $clientRepository,
        BaseHttpResponse $response
    )
    {
        $clientRepository->createPasswordGrantClient(null, $request->input('name'), 'http://localhost');
        return $response->setMessage(trans('plugins.api::api.create_new_client_success'));
    }

    /**
     * @param int $id
     * @param ClientRepository $clientRepository
     * @param FormBuilder $builder
     * @return string
     * @author Sang Nguyen
     */
    public function getEditClient($id, ClientRepository $clientRepository, FormBuilder $builder)
    {
        $client = $clientRepository->find($id);
        /**
         * @var ApiClientForm $form
         */
        $form = $builder->create(ApiClientForm::class, ['url' => route('api.clients.edit.post', $client->id)]);
        $form->setUseInlineJs(true)
            ->setModel($client)
            ->setTitle(trans('plugins.api::api.edit_client', ['name' => $client->name]));
        return $form->renderForm();
    }

    /**
     * @param int $id
     * @param ApiClientRequest $request
     * @param ClientRepository $clientRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postEditClient(
        $id,
        ApiClientRequest $request,
        ClientRepository $clientRepository,
        BaseHttpResponse $response
    )
    {
        $client = $clientRepository->find($id);
        if (!$client) {
            abort(404);
        }
        $clientRepository->update($client, $request->input('name'), $client->redirect);
        return $response->setMessage(trans('plugins.api::api.edit_client_success'));
    }

    /**
     * @param int $id
     * @param ClientRepository $clientRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     */
    public function getDeleteClient($id, ClientRepository $clientRepository)
    {
        $client = $clientRepository->find($id);
        return view('plugins.api::delete', compact('client'));
    }

    /**
     * @param int $id
     * @param ClientRepository $clientRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postDeleteClient($id, ClientRepository $clientRepository, BaseHttpResponse $response)
    {
        $client = $clientRepository->find($id);
        if (!$client) {
            abort(404);
        }
        $clientRepository->delete($client);
        return $response->setMessage(trans('plugins.api::api.delete_success'));
    }
}
