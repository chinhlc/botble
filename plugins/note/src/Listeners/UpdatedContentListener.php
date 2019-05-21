<?php

namespace Botble\Note\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Exception;
use Note;

class UpdatedContentListener
{

    /**
     * Handle the event.
     *
     * @param UpdatedContentEvent $event
     * @return void
     * @author Sang Nguyen
     */
    public function handle(UpdatedContentEvent $event)
    {
        try {
            Note::saveNote($event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
