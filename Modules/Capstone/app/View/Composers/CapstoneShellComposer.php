<?php

namespace Modules\Capstone\View\Composers;

use Illuminate\View\View;

/**
 * Layout Shell composer: provides safe defaults for Capstone chrome.
 *
 * Page data comes from BladeController::page(); this composer only ensures
 * layouts/components never hit undefined variables when rendered standalone
 * in tests or previews. It never queries the database.
 */
final class CapstoneShellComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();

        $view->with('actor', $data['actor'] ?? null);
        $view->with('activeRole', $data['activeRole'] ?? null);
        $view->with('pagePath', $data['pagePath'] ?? '/');
        $view->with('pageParams', $data['pageParams'] ?? []);
        $view->with('featureAccess', $data['featureAccess'] ?? request()->attributes->get('capstone_feature_access', []));
    }
}
