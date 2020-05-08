<?php

namespace Botble\CustomField\Providers;

use Assets;
use Illuminate\Support\Facades\Auth;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\CustomField\Facades\CustomFieldSupportFacade;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'handle'], 125, 3);
    }

    /**
     * @param string $location
     * @param string $screen
     * @param \Eloquent $object
     * @throws \Throwable
     */
    public function handle($screen, $priority, $object = null)
    {
        if ($screen !== CUSTOM_FIELD_MODULE_SCREEN_NAME && $priority == 'advanced') {
            $hasCustomFields = false;

            /**
             * Every models will have these rules by default
             */
            if (Auth::check()) {
                add_custom_fields_rules_to_check([
                    'logged_in_user'          => Auth::user()->getKey(),
                    'logged_in_user_has_role' => $this->app->make(RoleInterface::class)->pluck('id'),
                ]);
            }

            if (defined('PAGE_MODULE_SCREEN_NAME')) {
                switch ($screen) {
                    case PAGE_MODULE_SCREEN_NAME:
                        add_custom_fields_rules_to_check([
                            'page_template' => isset($object->template) ? $object->template : '',
                            'page'          => isset($object->id) ? $object->id : '',
                            'model_name'    => PAGE_MODULE_SCREEN_NAME,
                        ]);
                        $hasCustomFields = true;
                        break;
                }
            }

            if (defined('POST_MODULE_SCREEN_NAME')) {
                switch ($screen) {
                    case CATEGORY_MODULE_SCREEN_NAME:
                        add_custom_fields_rules_to_check([
                            CATEGORY_MODULE_SCREEN_NAME => isset($object->id) ? $object->id : null,
                            'model_name'                => CATEGORY_MODULE_SCREEN_NAME,
                        ]);
                        $hasCustomFields = true;
                        break;
                    case POST_MODULE_SCREEN_NAME:
                        add_custom_fields_rules_to_check([
                            'model_name' => POST_MODULE_SCREEN_NAME,
                        ]);
                        if ($object) {
                            $relatedCategoryIds = $this->app->make(PostInterface::class)->getRelatedCategoryIds($object);
                            add_custom_fields_rules_to_check([
                                POST_MODULE_SCREEN_NAME . '.post_with_related_category' => $relatedCategoryIds,
                                POST_MODULE_SCREEN_NAME . '.post_format'                => $object->format_type,
                            ]);
                        }
                        $hasCustomFields = true;
                        break;
                }
            }

            if ($hasCustomFields) {
                echo $this->render($screen, isset($object->id) ? $object->id : null);
            }
        }
    }

    /**
     * @param $screen
     * @param $id
     * @throws \Throwable
     */
    protected function render($screen, $id)
    {
        $customFieldBoxes = get_custom_field_boxes($screen, $id);

        if (!$customFieldBoxes) {
            return null;
        }

        Assets::addStylesDirectly([
            'vendor/core/plugins/custom-field/css/custom-field.css',
        ])
            ->addScriptsDirectly(config('core.base.general.editor.ckeditor.js'))
            ->addScriptsDirectly([
                'vendor/core/plugins/custom-field/js/use-custom-fields.js',
            ])
            ->addScripts(['jquery-ui']);

        CustomFieldSupportFacade::renderAssets();

        return CustomFieldSupportFacade::renderCustomFieldBoxes($customFieldBoxes);
    }
}
