<div id="{{ $chart->getElementId() }}" style="width: 100%; display: block; min-height: 300px;"></div>

@push('footer')
    <script type="text/javascript">
        jQuery(function () {
            `use strict`;

            Morris.{{ $chart->__chart_type }}(
                    {!! $chart->toJSON() !!}
            );
        });
    </script>
@endpush

@if ($chart->isUseInlineJs())
    {!! Assets::getStylesheetItemToHtml('morris') !!}
    {!! Assets::getJavascriptItemToHtml('raphael') !!}
    {!! Assets::getJavascriptItemToHtml('morris') !!}

    <script type="text/javascript">
        jQuery(function () {
            `use strict`;

            Morris.{{ $chart->__chart_type }}(
                    {!! $chart->toJSON() !!}
            );
        });
    </script>
@else
    @push('footer')
        <script type="text/javascript">
            jQuery(function () {
                `use strict`;

                Morris.{{ $chart->__chart_type }}(
                        {!! $chart->toJSON() !!}
                );
            });
        </script>
    @endpush
@endif