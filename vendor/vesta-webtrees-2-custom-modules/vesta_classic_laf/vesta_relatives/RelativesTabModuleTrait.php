<?php

namespace Cissee\Webtrees\Module\Relatives;

use Cissee\WebtreesExt\MoreI18N;
use Fisharebest\Webtrees\I18N;
use Vesta\CommonI18N;
use Vesta\ControlPanelUtils\Model\ControlPanelCheckbox;
use Vesta\ControlPanelUtils\Model\ControlPanelPreferences;
use Vesta\ControlPanelUtils\Model\ControlPanelSection;
use Vesta\ControlPanelUtils\Model\ControlPanelSubsection;
use Vesta\ModuleI18N;

trait RelativesTabModuleTrait {

  protected function getMainTitle() {
    return CommonI18N::titleVestaRelatives();
  }

  public function getShortDescription() {
    $part2 = I18N::translate('Replacement for the original \'Families\' module.');
    if (!$this->isEnabled()) {
      $part2 = ModuleI18N::translate($this, $part2);
    }
    return MoreI18N::xlate('A tab showing the close relatives of an individual.') . ' ' . $part2;            
  }

  protected function getFullDescription() {
    $description = array();
    $description[] = /* I18N: Module Configuration */I18N::translate('An extended \'Families\' tab, with hooks for other custom modules.');
    $description[] = /* I18N: Module Configuration */I18N::translate('Intended as a replacement for the original \'Families\' module.');
    $description[] = CommonI18N::requires1(CommonI18N::titleVestaCommon());
    return $description;
  }

  protected function createPrefs() {
    $generalSub = array();
    $generalSub[] = new ControlPanelSubsection(
            CommonI18N::displayedTitle(),
            array(/*new ControlPanelCheckbox(                    
                I18N::translate('Include the %1$s symbol in the module title', $this->getVestaSymbol()),
                null,
                'VESTA',
                '1'),*/
        new ControlPanelCheckbox(
                CommonI18N::vestaSymbolInTabTitle(),
                CommonI18N::vestaSymbolInTitle2(),
                'VESTA_TAB',
                '1')));
    
    $generalSub[] = new ControlPanelSubsection(
            /* I18N: Module Configuration */I18N::translate('Initialization'),
            array(
        new ControlPanelCheckbox(
                /* I18N: Module Configuration */I18N::translate('Load this tab dynamically'),
                /* I18N: Module Configuration */I18N::translate('Select this option in order to load the tab contents only once the tab is actually selected. This may improve the overall performance.') . ' ' .
                /* I18N: Module Configuration */I18N::translate('Deselect this option if you have other tabs which use this tab\'s content (the Album tab in particular displays all media from all tabs in the slideshow, and will therefore work better with this option deselected).'),
                'CAN_LOAD_AJAX',
                '1')));

    $sections = array();
    $sections[] = new ControlPanelSection(
            CommonI18N::general(),
            null,
            $generalSub);

    return new ControlPanelPreferences($sections);
  }

}
