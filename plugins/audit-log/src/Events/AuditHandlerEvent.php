<?php

namespace Botble\AuditLog\Events;

use Auth;
use Illuminate\Queue\SerializesModels;

class AuditHandlerEvent extends \Event
{
    use SerializesModels;

    /**
     * @var mixed
     */
    public $module;

    /**
     * @var mixed
     */
    public $action;

    /**
     * @var mixed
     */
    public $reference_id;

    /**
     * @var mixed
     */
    public $reference_user;

    /**
     * @var string
     */
    public $reference_name;

    /**
     * @var string
     */
    public $type;

    /**
     * AuditHandlerEvent constructor.
     * @param string $module
     * @param string $action
     * @param int $reference_id
     * @param null $reference_name
     * @param string $type
     * @param int $reference_user
     * @author Sang Nguyen
     */
    public function __construct($module, $action, $reference_id = 0, $reference_name, $type, $reference_user = 0)
    {
        if ($reference_user === 0) {
            $reference_user = Auth::user()->getKey();
        }
        $this->module = $module;
        $this->action = $action;
        $this->reference_user = $reference_user;
        $this->reference_id = $reference_id;
        $this->reference_name = $reference_name;
        $this->type = $type;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     * @author Sang Nguyen
     */
    public function broadcastOn()
    {
        return [];
    }
}
