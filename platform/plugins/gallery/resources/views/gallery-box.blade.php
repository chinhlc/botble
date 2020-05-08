{!! Form::hidden('gallery', $value ? json_encode($value) : null, ['id' => 'gallery-data', 'class' => 'form-control']) !!}
<div>
    <div class="list-photos-gallery">
        <div class="row" id="list-photos-items">
            @if (!empty($value))
                @foreach ($value as $key => $item)
                    <div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="{{ $key }}" data-img="{{ Arr::get($item, 'img') }}" data-description="{{ Arr::get($item, 'description') }}">
                        <div class="gallery_image_wrapper">
                            <img src="{{ get_image_url(Arr::get($item, 'img'), 'thumb') }}" alt="{{ trans('plugins/gallery::gallery.item') }}">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group">
        <a href="#" class="btn_select_gallery">{{ trans('plugins/gallery::gallery.select_image') }}</a>&nbsp;
        <a href="#" class="text-danger reset-gallery @if (empty($value)) hidden @endif">{{ trans('plugins/gallery::gallery.reset') }}</a>
    </div>
</div>

<div id="edit-gallery-item" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('plugins/gallery::gallery.update_photo_description') }}</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body with-padding">
                <p><input type="text" class="form-control" id="gallery-item-description" placeholder="{{ trans('plugins/gallery::gallery.update_photo_description_placeholder') }}"></p>
            </div>

            <div class="modal-footer">
                <button class="float-left btn btn-danger" id="delete-gallery-item" href="#">{{ trans('plugins/gallery::gallery.delete_photo') }}</button>
                <button class="float-right btn btn-secondary" data-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                <button class="float-right btn btn-primary" id="update-gallery-item">{{ trans('core/base::forms.update') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- end Modal -->

@push('footer')
<script>
    "use strict";

    $(document).ready(function () {

        $('.btn_select_gallery').rvMedia({
            onSelectFiles: function (files) {
                var last_index = $('.list-photos-gallery .photo-gallery-item:last-child').data('id') + 1;
                $.each(files, function (index, file) {
                    $('.list-photos-gallery .row').append('<div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="' + (last_index + index) + '" data-img="' + file.url + '" data-description=""><div class="gallery_image_wrapper"><img src="' + file.thumb + '" /></div></div>');
                });
                initSortable();
                updateItems();
                $('.reset-gallery').removeClass('hidden');
            }
        });

        var initSortable = function () {
            let el = document.getElementById('list-photos-items');
            Sortable.create(el, {
                group: 'galleries', // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
                sort: true, // sorting inside list
                delay: 0, // time in milliseconds to define when the sorting should start
                disabled: false, // Disables the sortable if set to true.
                store: null, // @see Store
                animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
                handle: '.photo-gallery-item',
                ghostClass: 'sortable-ghost', // Class name for the drop placeholder
                chosenClass: 'sortable-chosen', // Class name for the chosen item
                dataIdAttr: 'data-id',

                forceFallback: false, // ignore the HTML5 DnD behaviour and force the fallback to kick in
                fallbackClass: 'sortable-fallback', // Class name for the cloned DOM Element when using forceFallback
                fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body

                scroll: true, // or HTMLElement
                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px

                // dragging ended
                onEnd: () => {
                    updateItems();
                }
            });
        };

        initSortable();

        var updateItems = function () {
            let items = [];
            $.each($('.photo-gallery-item'), (index, widget) => {
                $(widget).data('id', index);
                items.push({img: $(widget).data('img'), description: $(widget).data('description')});
            });

            $('#gallery-data').val(JSON.stringify(items));
        };

        var list_photo_gallery = $('.list-photos-gallery');
        var edit_gallery_modal = $('#edit-gallery-item');

        $('.reset-gallery').on('click', function (event) {
            event.preventDefault();
            $('.list-photos-gallery .photo-gallery-item').remove();
            updateItems();
            $(this).addClass('hidden');
        });

        list_photo_gallery.on('click', '.photo-gallery-item', function () {
            var id = $(this).data('id');
            $('#delete-gallery-item').data('id', id);
            $('#update-gallery-item').data('id', id);
            $('#gallery-item-description').val($(this).data('description'));
            edit_gallery_modal.modal('show');
        });

        edit_gallery_modal.on('click', '#delete-gallery-item', function (event) {
            event.preventDefault();
            edit_gallery_modal.modal('hide');
            list_photo_gallery.find('.photo-gallery-item[data-id=' + $(this).data('id') + ']').remove();
            updateItems();
            if (list_photo_gallery.find('.photo-gallery-item').length === 0) {
                $('.reset-gallery').addClass('hidden');
            }
        });

        edit_gallery_modal.on('click', '#update-gallery-item', function (event) {
            event.preventDefault();
            edit_gallery_modal.modal('hide');
            list_photo_gallery.find('.photo-gallery-item[data-id=' + $(this).data('id') + ']').data('description', $('#gallery-item-description').val());
            updateItems();
        });
    });
</script>
@endpush
