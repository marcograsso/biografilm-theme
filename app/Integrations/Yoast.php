<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;
use Yard\Hook\Filter;

#[IsPluginActive("wordpress-seo/wp-seo.php")]
class Yoast
{
    #[Filter("wpseo_title")]
    public function fix_archive_titles(string $title): string
    {
        if (is_post_type_archive('film')) {
            return 'Tutti i film — ' . get_bloginfo('name');
        }

        return $title;
    }
}
