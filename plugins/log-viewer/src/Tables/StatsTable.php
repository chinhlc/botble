<?php

namespace Botble\LogViewer\Tables;

use Botble\LogViewer\Bases\Table;
use Botble\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;

class StatsTable extends Table
{
    /**
     * Make a stats table instance.
     *
     * @param  array $data
     * @param  \Botble\LogViewer\Contracts\Utilities\LogLevels $levels
     * @param  string|null $locale
     *
     * @return \Botble\LogViewer\Tables\StatsTable
     * @author ARCANEDEV
     */
    public static function make(array $data, LogLevelsContract $levels, $locale = null)
    {
        return new self($data, $levels, $locale);
    }

    /**
     * Get json chart data.
     *
     * @param  string|null $locale
     *
     * @return array
     * @author ARCANEDEV
     */
    public function totalsJson($locale = null)
    {
        $this->setLocale($locale);

        $json = [];
        $levels = array_except($this->footer(), 'all');

        foreach ($levels as $level => $count) {
            $json[] = [
                'label' => $this->translate('levels.' . $level),
                'value' => $count,
                'color' => $this->color($level),
                'highlight' => $this->color($level),
            ];
        }

        return json_encode(array_values($json), JSON_PRETTY_PRINT);
    }

    /**
     * Prepare table header.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    protected function prepareHeader(array $data)
    {
        return array_merge_recursive(
            [
                'date' => $this->translate('general.date'),
                'all' => $this->translate('general.all'),
            ],
            $this->levels->names($this->locale)
        );
    }

    /**
     * Prepare table rows.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    protected function prepareRows(array $data)
    {
        $rows = [];

        foreach ($data as $date => $levels) {
            $rows[$date] = array_merge(compact('date'), $levels);
        }

        return $rows;
    }

    /**
     * Prepare table footer.
     *
     * @param  array $data
     *
     * @return array
     * @author ARCANEDEV
     */
    protected function prepareFooter(array $data)
    {
        $footer = [];

        foreach ($data as $levels) {
            foreach ($levels as $level => $count) {
                if (!isset($footer[$level])) {
                    $footer[$level] = 0;
                }

                $footer[$level] += $count;
            }
        }

        return $footer;
    }
}
