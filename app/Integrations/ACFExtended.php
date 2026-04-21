<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Action;
use Yard\Hook\Filter;

#[IsPluginActive("acf-extended-pro/acf-extended.php")]
class ACFExtended
{
    public function __construct()
    {
        add_filter("acfe/flexible/thumbnail", [$this, "flexible_layout_thumbnail"], 10, 3);
    }

    #[Action("acfe/init")]
    public function enable_classic_editor()
    {
        acfe_update_setting("modules/classic_editor", true);
    }

    public function flexible_layout_thumbnail($thumbnail, $field, $layout)
    {
        $theme_dir = get_stylesheet_directory();
        $theme_url = get_stylesheet_directory_uri();

        $folder_name    = str_replace("_", "-", $layout["name"]);
        $thumbnail_path = $theme_dir . "/views/components/" . $folder_name . "/thumbnail.png";
        $thumbnail_url  = $theme_url . "/views/components/" . $folder_name . "/thumbnail.png";

        if (file_exists($thumbnail_path)) {
            return $thumbnail_url;
        }

        return $theme_url . "/assets/images/fallback-img.jpg";
    }
}
