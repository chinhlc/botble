<?php

namespace Botble\Api\Http\Controllers;

use Botble\Api\Http\Requests\LoginRequest;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class ApiController extends BaseController
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * AuthenticationController constructor.
     *
     * @param Client $httpClient
     * @param ClientRepository $clientRepository
     */
    public function __construct(Client $httpClient, ClientRepository $clientRepository)
    {
        $this->httpClient = $httpClient;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param LoginRequest $request
     * @param BaseHttpResponse $response
     *
     * @return mixed
     */
    public function login(LoginRequest $request, BaseHttpResponse $response)
    {
        $client = Passport::client()
            ->where('password_client', 1)
            ->first();

        if (!$client) {
            return $response
                ->setError()
                ->setMessage(__('No client found, please make sure that you installed Passport data!'));
        }

        try {
            $data = $this->httpClient->post(url('/oauth/token'), [
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => $client->id,
                    'client_secret' => $client->secret,
                    'username'      => $request->input('email'),
                    'password'      => $request->input('password'),
                    'scope'         => '*',
                ],
            ]);
        } catch (Exception $exception) {
            return $response
                ->setError(true)
                ->setMessage($exception->getMessage())
                ->setCode(401);
        }

        return $response
            ->setData(json_decode((string)$data->getBody(), true));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function logout(Request $request, BaseHttpResponse $response)
    {
        $request->user()->token()->delete();
        return $response;
    }
}
