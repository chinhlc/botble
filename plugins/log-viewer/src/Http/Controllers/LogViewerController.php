<?php

namespace Botble\LogViewer\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\LogViewer\Exceptions\LogNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use LogViewer;

class LogViewerController extends BaseController
{

    /**
     * @var int|mixed
     */
    protected $perPage = 30;

    /**
     * @var string
     */
    protected $showRoute = 'log-viewer::logs.show';

    /**
     * LogViewerController constructor.
     * @author ARCANEDEV
     */
    public function __construct()
    {
        $this->perPage = config('plugins.log-viewer.general.per-page', $this->perPage);
        Assets::addJavascript(['moment', 'datetimepicker'])
            ->addStylesheets(['datetimepicker']);
    }

    /**
     * List all logs.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     * @author ARCANEDEV
     */
    public function listLogs(Request $request)
    {
        page_title()->setTitle(trans('plugins.log-viewer::general.name'));

        Assets::addStylesheetsDirectly(['/vendor/core/plugins/log-viewer/css/log-viewer.css']);

        $stats = LogViewer::statsTable();
        $headers = $stats->header();
        $rows = $this->paginate($stats->rows(), $request);

        return view('plugins.log-viewer::logs', compact('headers', 'rows'));
    }

    /**
     * Paginate logs.
     *
     * @param  array $data
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @author ARCANEDEV
     */
    protected function paginate(array $data, Request $request)
    {
        $page = $request->get('page', 1);
        $offset = ($page * $this->perPage) - $this->perPage;
        $items = array_slice($data, $offset, $this->perPage, true);
        $rows = new LengthAwarePaginator($items, count($data), $this->perPage, $page);

        $rows->setPath($request->url());

        return $rows;
    }

    /**
     * Show the log.
     *
     * @param  string $date
     *
     * @return \Illuminate\View\View
     * @author ARCANEDEV
     */
    public function show($date)
    {
        page_title()->setTitle(trans('plugins.log-viewer::general.name') . ' ' . $date);

        $log = $this->getLogOrFail($date);
        $levels = LogViewer::levelsNames();
        $entries = $log->entries()->paginate($this->perPage);

        return view('plugins.log-viewer::show', compact('log', 'levels', 'entries'));
    }

    /**
     * @param $date
     * @return \Botble\LogViewer\Entities\Log|null
     * @author ARCANEDEV
     */
    protected function getLogOrFail($date)
    {
        try {
            return LogViewer::get($date);
        } catch (LogNotFoundException $ex) {
            abort(404, $ex->getMessage());
        }
        return null;
    }

    /**
     * Filter the log entries by level.
     *
     * @param  string $date
     * @param  string $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     * @author ARCANEDEV
     */
    public function showByLevel($date, $level)
    {
        page_title()->setTitle(trans('plugins.log-viewer::general.name') . ' ' . $date);

        $log = $this->getLogOrFail($date);

        if ($level === 'all') {
            return redirect()->route($this->showRoute, [$date]);
        }

        $levels = LogViewer::levelsNames();
        $entries = LogViewer::entries($date, $level)->paginate($this->perPage);

        return view('plugins.log-viewer::show', compact('log', 'levels', 'entries'));
    }

    /**
     * Download the log
     *
     * @param  string $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author ARCANEDEV
     */
    public function download($date)
    {
        return LogViewer::download($date);
    }

    /**
     * Delete a log.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @author ARCANEDEV
     */
    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            abort(405, 'Method Not Allowed');
        }

        $date = $request->get('date');

        return response()->json([
            'result' => LogViewer::delete($date) ? 'success' : 'error'
        ]);
    }
}
