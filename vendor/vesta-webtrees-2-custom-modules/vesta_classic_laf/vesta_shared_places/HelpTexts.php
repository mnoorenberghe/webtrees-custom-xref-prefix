<?php

namespace Cissee\Webtrees\Module\SharedPlaces;

use Cissee\WebtreesExt\MoreI18N;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Vesta\CommonI18N;
use Vesta\HelpTextsPlaceHistory;
use function view;

class HelpTexts {

    public static function helpText($help) {
        switch ($help) {
            case 'Summary':
                $title = I18N::translate('Shared place summary');
                $text = '<p>' .
                    I18N::translate('The summary shows the shared place data, formatted in the same way as for events with a place mapped to the respective shared place.') . ' ' .
                    I18N::translate('Therefore, the place name is displayed here including the full hierarchy.') . ' ' .
                    I18N::translate('You can set a reference year (which may be evaluated by other modules, such as %1$s) in the module configuration.', CommonI18N::titleVestaGov4Webtrees()) .
                    '</p>';
                break;
            case 'PLAC':
                $title = MoreI18N::xlate('Shared place name');
                $text = '<p>' .
                    I18N::translate('Place names should be entered as single place name (do not use a comma-separated list here).') . ' ' .
                    I18N::translate('Use the separate tag \'%1$s\' in order to model a place hierarchy.', GedcomTag::getLabel('_LOC:_LOC')) .
                    '</p>' .
                    '<p>' .
                    I18N::translate('Place names can change over time. You can add multiple names to a shared place, and indicate historic names via a suitable date range.') .
                    '</p>';
                break;
            case 'PLAC_CSV':
                $title = MoreI18N::xlate('Shared place name');
                $text = '<p>' .
                    I18N::translate('Place names should be entered as a comma-separated list, starting with the smallest place and ending with the country. For example, “Westminster, London, England”.') .
                    '</p>' .
                    '<p>' .
                    I18N::translate('Place names can change over time. You can add multiple names to a shared place, and indicate historic names via a suitable date range.') .
                    '</p>';
                break;
            default:
                return HelpTextsPlaceHistory::helpText($help);
        }

        return view('modals/help', [
            'title' => $title,
            'text' => $text,
        ]);
    }

}
