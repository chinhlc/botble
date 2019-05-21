<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Events\SessionStarted;
use Event;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession as IlluminateStartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;

class StartSessionMiddleware extends IlluminateStartSession
{
    /**
     * StartSession constructor.
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $manager)
    {
        parent::__construct($manager);
    }

    /**
     * Start the session for the given request.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Session\Session | Store
     */
    protected function startSession(Request $request)
    {
        /**
         * @var Store $session
         */
        $session = parent::startSession($request);

        Event::fire(new SessionStarted($session));

        return $session;
    }
}
