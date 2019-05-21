<?php

namespace Botble\Note\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Note\Repositories\Interfaces\NoteInterface;

class HookServiceProvider extends ServiceProvider
{

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     */
    public function boot()
    {
        add_filter(BASE_FILTER_REGISTER_CONTENT_TABS, [$this, 'addNoteTab'], 50, 3);
        add_filter(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, [$this, 'addNoteContent'], 50, 3);
    }

    /**
     * @param $tabs
     * @param $screen
     * @return string
     * @author Sang Nguyen
     * @since 2.0
     * @throws \Throwable
     */
    public function addNoteTab($tabs, $screen, $data = null)
    {
        if (in_array($screen, config('plugins.note.general.supported'))) {
            if (!empty($data)) {
                $tabs = $tabs . view('plugins.note::history-tab')->render();
            }
            return $tabs . view('plugins.note::tab')->render();
        }
        return $tabs;
    }

    /**
     * @param $tabs
     * @param $screen
     * @param \Eloquent $data
     * @return string
     * @author Sang Nguyen
     * @since 2.0
     * @throws \Throwable
     */
    public function addNoteContent($tabs, $screen, $data = null)
    {
        if (in_array($screen, config('plugins.note.general.supported'))) {
            $notes = [];
            if (!empty($data)) {
                $notes = $this->app->make(NoteInterface::class)->allBy([
                    'reference_id' => $data->id,
                    'reference_type' => $screen,
                ]);
                $tabs = $tabs . view('plugins.note::history-content', ['model' => $data])->render();
            }
            return $tabs . view('plugins.note::content', compact('notes'))->render();
        }
        return $tabs;
    }
}
