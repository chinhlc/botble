<?php

namespace Botble\Page\Supports;

class Template
{
    /**
     * @var array
     */
    protected static $templates = [];

    /**
     * @param $templates
     * @return void
     * @author Sang Nguyen
     * @since 16-09-2016
     */
    public static function registerPageTemplate($templates = [])
    {
        foreach ($templates as $key => $template) {
            if (in_array($key, self::getExistsTemplate())) {
                self::$templates[$key] = $template;
            }
        }
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 16-09-2016
     */
    protected static function getExistsTemplate()
    {
        $files = scan_folder(public_path() . DIRECTORY_SEPARATOR . config('core.theme.general.themeDir') . DIRECTORY_SEPARATOR . setting('theme') . DIRECTORY_SEPARATOR . config('core.theme.general.containerDir.layout'));
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.blade.php', '', $file);
        }
        return $files;
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 16-09-2016
     */
    public static function getPageTemplates()
    {
        return self::$templates;
    }
}
