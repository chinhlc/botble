/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 57);
/******/ })
/************************************************************************/
/******/ ({

/***/ 57:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(58);


/***/ }),

/***/ 58:
/***/ (function(module, exports) {

var MenuNestable = function () {
    return {
        props: {
            nestable: $('#nestable')
        },
        setDataItem: function setDataItem(target) {
            target.each(function (index, el) {
                var current = $(el);
                current.data('id', current.attr('data-id'));
                current.data('title', current.attr('data-title'));
                current.data('related-id', current.attr('data-related-id'));
                current.data('type', current.attr('data-type'));
                current.data('custom-url', current.attr('data-custom-url'));
                current.data('class', current.attr('data-class'));
                current.data('target', current.attr('data-target'));
            });
        },
        updatePositionForSerializedObj: function updatePositionForSerializedObj(arr_obj) {
            var result = arr_obj;
            $.each(result, function (index, val) {
                val.position = index;
                if (typeof val.children == 'undefined') {
                    val.children = [];
                }
                MenuNestable.updatePositionForSerializedObj(val.children);
            });
            return result;
        },
        //main function to initiate the module
        init: function init() {
            var depth = parseInt(MenuNestable.props.nestable.attr('data-depth'));
            if (depth < 1) {
                depth = 5;
            }
            $('.nestable-menu').nestable({
                group: 1,
                maxDepth: depth,
                expandBtnHTML: '',
                collapseBtnHTML: ''
            });
        },
        handleNestableMenu: function handleNestableMenu() {
            //Show node details
            $(document).on('click', '.dd-item .dd3-content a.show-item-details', function (event) {
                event.preventDefault();
                var parent = $(this).parent().parent();
                $(this).toggleClass('active');
                parent.toggleClass('active');
            });

            // Edit attr
            $(document).on('change blur keyup', '.nestable-menu .item-details input[type="text"], .nestable-menu .item-details select', function (event) {
                event.preventDefault();
                var current = $(this);
                var parent = current.closest('li.dd-item');
                parent.attr('data-' + current.attr('name'), current.val());
                parent.data(current.attr('name'), current.val());
                parent.find('> .dd3-content .text[data-update="' + current.attr('name') + '"]').text(current.val());
                if (current.val().trim() == '') {
                    parent.find('> .dd3-content .text[data-update="' + current.attr('name') + '"]').text(current.attr('data-old'));
                }
                MenuNestable.setDataItem(MenuNestable.props.nestable.find('> ol.dd-list li.dd-item'));
            });

            // Add nodes
            $(document).on('click', '.box-links-for-menu .btn-add-to-menu', function (event) {
                event.preventDefault();
                var current = $(this);
                var parent = current.parents('.the-box');
                var html = '';
                if (parent.attr('id') == 'external_link') {
                    var data_type = 'custom-link';
                    var data_related_id = 0;
                    var data_title = $('#node-title').val();
                    var data_url = $('#node-url').val();
                    var data_css_class = $('#node-css').val();
                    var data_font_icon = $('#node-icon').val();
                    var data_target = $('#target').find('option:selected').val();
                    var url_html = '<label class="pad-bot-5"><span class="text pad-top-5 dis-inline-block" data-update="custom-url">Url</span><input type="text" data-old="' + data_url + '" value="' + data_url + '" name="custom-url"></label>';
                    html += '<li data-type="' + data_type + '" data-related-id="' + data_related_id + '" data-title="' + data_title + '" data-class="' + data_css_class + '" data-id="0" data-custom-url="' + data_url + '" data-icon-font="' + data_font_icon + '" data-target="' + data_target + '" class="dd-item dd3-item">';
                    html += '<div class="dd-handle dd3-handle"></div>';
                    html += '<div class="dd3-content">';
                    html += '<span class="text pull-left" data-update="title">' + data_title + '</span>';
                    html += '<span class="text pull-right">' + data_type + '</span>';
                    html += '<a href="#" class="show-item-details"><i class="fa fa-angle-down"></i></a>';
                    html += '<div class="clearfix"></div>';
                    html += '</div>';
                    html += '<div class="item-details">';
                    html += '<label class="pad-bot-5">';
                    html += '<span class="text pad-top-5 dis-inline-block" data-update="title">Title</span>';
                    html += '<input type="text" data-old="' + data_title + '" value="' + data_title + '" name="title" class="form-control">';
                    html += '</label>';
                    html += url_html;
                    html += '<label class="pad-bot-5 dis-inline-block"><span class="text pad-top-5" data-update="icon-font">Icon - font</span><input type="text" name="icon-font" value="' + data_font_icon + '" data-old="' + data_font_icon + '" class="form-control"></label>';
                    html += '<label class="pad-bot-10">';
                    html += '<span class="text pad-top-5 dis-inline-block" data-update="class">CSS class</span>';
                    html += '<input type="text" data-old="' + data_css_class + '" value="' + data_css_class + '" name="class" class="form-control">';
                    html += '</label>';
                    html += '<label class="pad-bot-10">';
                    html += '<span class="text pad-top-5 dis-inline-block" data-update="target">Target</span>';
                    html += '<div style="width: 228px; display: inline-block"><select name="target" id="target" data-old="' + data_target + '" class="form-control select-full">';
                    html += '<option value="_self">Open link directly</option>';
                    html += '<option value="_blank" ' + (data_target == '_blank' ? 'selected="selected"' : '') + '>Open link in new tab</option>';
                    html += '</select></div>';
                    html += '</label>';
                    html += '<div class="text-right">';
                    html += '<a class="btn red btn-remove" href="#">Remove</a>';
                    html += '<a class="btn blue btn-cancel" href="#">Cancel</a>';
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="clearfix"></div>';
                    html += '</li>';
                    parent.find('input[type="text"]').val('');
                } else {
                    parent.find('.list-item li.active').each(function (index, el) {
                        var find_in = $(el).find('> label');
                        var data_type = find_in.attr('data-type');
                        var data_related_id = find_in.attr('data-related-id');
                        var data_title = find_in.attr('data-title');

                        html += '<li data-type="' + data_type + '" data-related-id="' + data_related_id + '" data-title="' + data_title + '" data-id="0" data-target="_self" class="dd-item dd3-item">';
                        html += '<div class="dd-handle dd3-handle"></div>';
                        html += '<div class="dd3-content">';
                        html += '<span class="text pull-left" data-update="title">' + data_title + '</span>';
                        html += '<span class="text pull-right">' + data_type + '</span>';
                        html += '<a href="#" class="show-item-details"><i class="fa fa-angle-down"></i></a>';
                        html += '<div class="clearfix"></div>';
                        html += '</div>';
                        html += '<div class="item-details">';
                        html += '<label class="pad-bot-5">';
                        html += '<span class="text pad-top-5 dis-inline-block" data-update="title">Title</span>';
                        html += '<input type="text" data-old="' + data_title + '" value="' + data_title + '" name="title" class="form-control">';
                        html += '</label>';
                        html += '<label class="pad-bot-5 dis-inline-block"><span class="text pad-top-5" data-update="icon-font">Icon - font</span><input type="text" name="icon-font" class="form-control"></label>';
                        html += '<label class="pad-bot-10">';
                        html += '<span class="text pad-top-5 dis-inline-block" data-update="class">CSS class</span>';
                        html += '<input type="text" name="class" class="form-control">';
                        html += '</label>';
                        html += '<label class="pad-bot-10">';
                        html += '<span class="text pad-top-5 dis-inline-block" data-update="target">Target</span>';
                        html += '<div style="width: 228px; display: inline-block"><select name="target" id="target" class="form-control select-full">';
                        html += '<option value="_self">Open link directly</option>';
                        html += '<option value="_blank">Open link in new tab</option>';
                        html += '</select></div>';
                        html += '</label>';
                        html += '<div class="text-right">';
                        html += '<a class="btn red btn-remove" href="#">Remove</a>';
                        html += '<a class="btn blue btn-cancel" href="#">Cancel</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="clearfix"></div>';
                        html += '</li>';

                        $(el).find('input[type=checkbox]').prop('checked', false);
                    });
                }
                // Create html
                $('.nestable-menu > ol.dd-list').append(html);

                $('.nestable-menu').find('.select-full').select2({
                    width: '100%',
                    minimumResultsForSearch: -1
                });

                // Change json
                MenuNestable.setDataItem(MenuNestable.props.nestable.find('> ol.dd-list li.dd-item'));
                parent.find('.list-item li.active').removeClass('active');
            });

            // Remove nodes
            $('.form-save-menu input[name="deleted_nodes"]').val('');
            $(document).on('click', '.nestable-menu .item-details .btn-remove', function (event) {
                event.preventDefault();
                var current = $(this);
                var dd_item = current.parents('.item-details').parent();

                var $elm = $('.form-save-menu input[name="deleted_nodes"]');
                //add id of deleted nodes to delete in controller
                $elm.val($elm.val() + ' ' + dd_item.attr('data-id'));
                var children = dd_item.find('> .dd-list').html();
                if (children != '' && children != null) {
                    dd_item.before(children);
                }
                dd_item.remove();
            });

            $(document).on('click', '.nestable-menu .item-details .btn-cancel', function (event) {
                event.preventDefault();
                var current_pa = $(this);
                var parent = current_pa.parents('.item-details').parent();
                parent.find('input[type="text"]').each(function (index, el) {
                    $(el).val($(el).attr('data-old'));
                });

                parent.find('select').each(function (index, el) {
                    $(el).val($(el).val());
                });

                parent.find('input[type="text"]').trigger('change');
                parent.find('select').trigger('change');
                parent.removeClass('active');
            });

            $(document).on('change', '.box-links-for-menu .list-item li input[type=checkbox]', function () {
                $(this).closest('li').toggleClass('active');
            });

            $(document).on('submit', '.form-save-menu', function () {
                if (MenuNestable.props.nestable.length < 1) {
                    $('#nestable-output').val('[]');
                } else {
                    var nestable_obj_returned = MenuNestable.props.nestable.nestable('serialize');
                    var the_obj = MenuNestable.updatePositionForSerializedObj(nestable_obj_returned);
                    $('#nestable-output').val(JSON.stringify(the_obj));
                }
            });

            var accordion = $('#accordion');

            function toggleChevron(e) {
                $(e.target).prev('.widget-heading').find('.narrow-icon').toggleClass('fa-angle-down fa-angle-up');
            }

            accordion.on('hidden.bs.collapse', toggleChevron);
            accordion.on('shown.bs.collapse', toggleChevron);

            Botble.callScroll($('.list-item'));
        }
    };
}();

$(window).load(function () {
    MenuNestable.init();
    MenuNestable.handleNestableMenu();
});

/***/ })

/******/ });