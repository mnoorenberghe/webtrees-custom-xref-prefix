<?php

namespace Cissee\WebtreesExt\Functions;

use Cissee\WebtreesExt\MoreI18N;
use Fisharebest\Webtrees\Webtrees;
use function route;
use function view;

//still used in 2.1
class FunctionsPrintExtHelpLink {

  //cf edit/input-addon-help.phtml
  public static function inputAddonHelp($module, string $topic): string {
    return
            '<div class="input-group-append">' .
            '    <span class="input-group-text">' .
            FunctionsPrintExtHelpLink::helpLink($module, $topic) .
            '    </span>' .
            '</div>';
  }
  
  //cf help/link.phtml
  public static function helpLink($module, string $topic): string {
    $url = route('module', [
        'module' => $module,
        'action' => 'Help',
        'topic' => $topic
    ]);

    if (str_starts_with(Webtrees::VERSION, '2.1')) {
        return
            '<a href="#" data-bs-toggle="modal" data-bs-backdrop="static" data-bs-target="#wt-ajax-modal" data-wt-href="' . $url . '" title="' . MoreI18N::xlate('Help') . '">' .
            view('icons/help') .
            '<span class="visually-hidden">' . MoreI18N::xlate('Help') . '</span>' .
            '</a>';
    }
    
    return
            '<a href="#" data-toggle="modal" data-target="#wt-ajax-modal" data-href="' . $url . '" title="' . MoreI18N::xlate('Help') . '">' .
            view('icons/help') .
            '<span class="sr-only">' . MoreI18N::xlate('Help') . '</span>' .
            '</a>';
  }

}
