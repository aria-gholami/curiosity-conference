<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class HideTabsAdmin extends Admin
{
    #[\Override]
    public function configureViews(ViewCollection $viewCollection): void
    {
        foreach ([
            'sulu_page.page_edit_form.seo',
            'sulu_page.page_edit_form.excerpt',
            'sulu_page.page_edit_form.settings',
        ] as $name) {
            if (!$viewCollection->has($name)) {
                continue;
            }

            /** @var object{setTabCondition: callable(string): void} $view */
            $view = $viewCollection->get($name);
            $view->setTabCondition('false');
        }
    }

    #[\Override]
    public static function getPriority(): int
    {
        return -10;
    }
}
