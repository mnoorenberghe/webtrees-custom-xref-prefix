<?php

namespace Cissee\WebtreesExt\Module;

use Cissee\WebtreesExt\ToggleableFactsCategory;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Cissee\WebtreesExt\AbstractModule; //required for $treeUsedForAccessLevelCheck hack
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleTabTrait;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;
use Vesta\Model\GenericViewElement;
use function view;

/**
 * Class IndividualFactsTabModule
 * [RC] patched
 */
class IndividualFactsTabModule_20 extends AbstractModule implements ModuleTabInterface {

  use ModuleTabTrait;

  /** @var ModuleService */
  protected $module_service;

  /** @var ClipboardService */
  protected $clipboard_service;
  protected $functionsPrintFacts;
  protected $viewName = 'modules/personal_facts/tab_20';

  public function setFunctionsPrintFacts($functionsPrintFacts) {
    $this->functionsPrintFacts = $functionsPrintFacts;
  }

  protected function getViewName(): string {
    return $this->viewName;
  }

  /**
   * IndividualFactsTabModule_2x constructor.
   *
   * @param ModuleService    $module_service
   * @param ClipboardService $clipboard_service
   */
  public function __construct(ModuleService $module_service, ClipboardService $clipboard_service) {
    $this->module_service = $module_service;
    $this->clipboard_service = $clipboard_service;
  }

  /**
   * How should this module be identified in the control panel, etc.?
   *
   * @return string
   */
  public function title(): string {
    /* I18N: Name of a module/tab on the individual page. */
    return I18N::translate('Facts and events');
  }

  /**
   * A sentence describing what this module does.
   *
   * @return string
   */
  public function description(): string {
    /* I18N: Description of the ???Facts and events??? module */
    return I18N::translate('A tab showing the facts and events of an individual.');
  }

  /**
   * The default position for this tab.  It can be changed in the control panel.
   *
   * @return int
   */
  public function defaultTabOrder(): int {
    return 1;
  }

  /**
   * A greyed out tab has no actual content, but may perhaps have
   * options to create content.
   *
   * @param Individual $individual
   *
   * @return bool
   */
  public function isGrayedOut(Individual $individual): bool {
    return false;
  }

  /**
   * Generate the HTML content of this tab.
   *
   * @param Individual $individual
   *
   * @return string
   */
  public function getTabContent(Individual $individual): string {
    // Only include events of close relatives that are between birth and death
    $min_date = $individual->getEstimatedBirthDate();
    $max_date = $individual->getEstimatedDeathDate();

    // Which facts and events are handled by other modules?
    $sidebar_facts = $this->module_service
            ->findByComponent(ModuleSidebarInterface::class, $individual->tree(), Auth::user())
            ->map(static function (ModuleSidebarInterface $sidebar): Collection {
      return $sidebar->supportedFacts();
    });

    $tab_facts = $this->module_service
            ->findByComponent(ModuleTabInterface::class, $individual->tree(), Auth::user())
            ->map(static function (ModuleTabInterface $sidebar): Collection {
      return $sidebar->supportedFacts();
    });

    $exclude_facts = $sidebar_facts->merge($tab_facts)->flatten();
        
    // The individual???s own facts
    $indifacts = $individual->facts()
            ->filter(static function (Fact $fact) use ($exclude_facts): bool {
      return !$exclude_facts->contains($fact->getTag());
    });
    
    // Add spouse-family facts
    foreach ($individual->spouseFamilies() as $family) {
      foreach ($family->facts() as $fact) {
        if (!$exclude_facts->contains($fact->getTag()) && $fact->getTag() !== 'CHAN') {
          $indifacts->push($fact);
        }
      }

      $spouse = $family->spouse($individual);

      if ($spouse instanceof Individual) {
        $spouse_facts = $this->spouseFacts($individual, $spouse, $min_date, $max_date);
        $indifacts = $indifacts->merge($spouse_facts);
      }

      $child_facts = $this->childFacts($individual, $family, '_CHIL', '', $min_date, $max_date);
      $indifacts = $indifacts->merge($child_facts);
    }

    $parent_facts = $this->parentFacts($individual, 1, $min_date, $max_date);
    $associate_facts = $this->associateFacts($individual);
    $historical_facts = $this->historicalFacts($individual);

    $indifacts = $indifacts
            ->merge($parent_facts)
            ->merge($associate_facts)
            ->merge($historical_facts);

    //[RC] ADDED
    $indifacts = $indifacts
            ->merge($this->additionalFacts($individual));

    $indifacts = Fact::sortFacts($indifacts);

    //[RC] additions		
    $show_relatives_facts = $individual->tree()->getPreference('SHOW_RELATIVES_EVENTS');
    $toggleableFactsCategories = $this->getToggleableFactsCategories($show_relatives_facts, !empty($historical_facts));

    //[RC] additions
    $outputBeforeTab = $this->getOutputBeforeTab($individual);
    $outputAfterTab = $this->getOutputAfterTab($individual);
    $outputInDescriptionbox = $this->getOutputInDescriptionbox($individual);
    $outputAfterDescriptionbox = $this->getOutputAfterDescriptionbox($individual);

    //[RC added]
    //fix #3807
    $usedFacts = $individual->facts();
        
    $view = view($this->getViewName(), [
        'can_edit' => $individual->canEdit(),
        'clipboard_facts' => $this->clipboard_service->pastableFacts($individual, $exclude_facts),
        'has_historical_facts' => $historical_facts !== [],
        'individual' => $individual,
        'facts' => $indifacts,
        //[RC] additions
        'usedFacts' => $usedFacts,
        
        'toggleableFactsCategories' => $toggleableFactsCategories,
        
        //probably needlesly generic; tab now has Vesta-specific aspects anyway!
        'printFactFunction' => function (Fact $fact) use ($individual) {
          return $this->functionsPrintFacts->printFactAndReturnScript($fact, $individual);
        },
                
        'outputBeforeTab' => $outputBeforeTab,
        'outputAfterTab' => $outputAfterTab,
        'outputInDescriptionbox' => $outputInDescriptionbox,
        'outputAfterDescriptionbox' => $outputAfterDescriptionbox,
                
        //additional config etc
        'module' => $this
    ]);

    return $view;
  }

  /**
   * Does a relative event occur within a date range (i.e. the individual's lifetime)?
   *
   * @param Fact $fact
   * @param Date $min_date
   * @param Date $max_date
   *
   * @return bool
   */
  private function includeFact(Fact $fact, Date $min_date, Date $max_date): bool {
    $fact_date = $fact->date();

    return $fact_date->isOK() && Date::compare($min_date, $fact_date) <= 0 && Date::compare($fact_date, $max_date) <= 0;
  }

  /**
   * Is this tab empty? If so, we don't always need to display it.
   *
   * @param Individual $individual
   *
   * @return bool
   */
  public function hasTabContent(Individual $individual): bool {
    return true;
  }

  /**
   * Can this tab load asynchronously?
   *
   * @return bool
   */
  public function canLoadAjax(): bool {
    return false;
  }

  /**
   * Convert an event into a special "event of a close relative".
   *
   * @param Fact   $fact
   * @param string $type
   *
   * @return Fact
   */
  private function convertEvent(Fact $fact, string $type): Fact {
    $gedcom = $fact->gedcom();
    $gedcom = preg_replace('/\n2 TYPE .*/', '', $gedcom);
    $gedcom = preg_replace('/^1 .*/', "1 EVEN CLOSE_RELATIVE\n2 TYPE " . $type, $gedcom);

    return new Fact($gedcom, $fact->record(), $fact->id());
  }

  /**
   * Spouse facts that are shown on an individual???s page.
   *
   * @param Individual $individual Show events that occured during the lifetime of this individual
   * @param Individual $spouse     Show events of this individual
   * @param Date       $min_date
   * @param Date       $max_date
   *
   * @return Fact[]
   */
  private function spouseFacts(Individual $individual, Individual $spouse, Date $min_date, Date $max_date): array {
    $SHOW_RELATIVES_EVENTS = $individual->tree()->getPreference('SHOW_RELATIVES_EVENTS');

    $death_of_a_spouse = [
        'DEAT' => [
            'M' => I18N::translate('Death of a husband'),
            'F' => I18N::translate('Death of a wife'),
            'U' => I18N::translate('Death of a spouse'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a husband'),
            'F' => I18N::translate('Burial of a wife'),
            'U' => I18N::translate('Burial of a spouse'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a husband'),
            'F' => I18N::translate('Cremation of a wife'),
            'U' => I18N::translate('Cremation of a spouse'),
        ],
    ];

    $facts = [];

    if (strpos($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU') !== false) {
      foreach ($spouse->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
        if ($this->includeFact($fact, $min_date, $max_date)) {
          $facts[] = $this->convertEvent($fact, $death_of_a_spouse[$fact->getTag()][$fact->record()->sex()]);
        }
      }
    }

    return $facts;
  }

  /**
   * Get the events of children and grandchildren.
   *
   * @param Individual $person
   * @param Family     $family
   * @param string     $option
   * @param string     $relation
   * @param Date       $min_date
   * @param Date       $max_date
   *
   * @return Fact[]
   */
  private function childFacts(Individual $person, Family $family, $option, $relation, Date $min_date, Date $max_date): array {
    $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

    $birth_of_a_child = [
        'BIRT' => [
            'M' => I18N::translate('Birth of a son'),
            'F' => I18N::translate('Birth of a daughter'),
            'U' => I18N::translate('Birth of a child'),
        ],
        'CHR' => [
            'M' => I18N::translate('Christening of a son'),
            'F' => I18N::translate('Christening of a daughter'),
            'U' => I18N::translate('Christening of a child'),
        ],
        'BAPM' => [
            'M' => I18N::translate('Baptism of a son'),
            'F' => I18N::translate('Baptism of a daughter'),
            'U' => I18N::translate('Baptism of a child'),
        ],
        'ADOP' => [
            'M' => I18N::translate('Adoption of a son'),
            'F' => I18N::translate('Adoption of a daughter'),
            'U' => I18N::translate('Adoption of a child'),
        ],
    ];

    $birth_of_a_sibling = [
        'BIRT' => [
            'M' => I18N::translate('Birth of a brother'),
            'F' => I18N::translate('Birth of a sister'),
            'U' => I18N::translate('Birth of a sibling'),
        ],
        'CHR' => [
            'M' => I18N::translate('Christening of a brother'),
            'F' => I18N::translate('Christening of a sister'),
            'U' => I18N::translate('Christening of a sibling'),
        ],
        'BAPM' => [
            'M' => I18N::translate('Baptism of a brother'),
            'F' => I18N::translate('Baptism of a sister'),
            'U' => I18N::translate('Baptism of a sibling'),
        ],
        'ADOP' => [
            'M' => I18N::translate('Adoption of a brother'),
            'F' => I18N::translate('Adoption of a sister'),
            'U' => I18N::translate('Adoption of a sibling'),
        ],
    ];

    $birth_of_a_half_sibling = [
        'BIRT' => [
            'M' => I18N::translate('Birth of a half-brother'),
            'F' => I18N::translate('Birth of a half-sister'),
            'U' => I18N::translate('Birth of a half-sibling'),
        ],
        'CHR' => [
            'M' => I18N::translate('Christening of a half-brother'),
            'F' => I18N::translate('Christening of a half-sister'),
            'U' => I18N::translate('Christening of a half-sibling'),
        ],
        'BAPM' => [
            'M' => I18N::translate('Baptism of a half-brother'),
            'F' => I18N::translate('Baptism of a half-sister'),
            'U' => I18N::translate('Baptism of a half-sibling'),
        ],
        'ADOP' => [
            'M' => I18N::translate('Adoption of a half-brother'),
            'F' => I18N::translate('Adoption of a half-sister'),
            'U' => I18N::translate('Adoption of a half-sibling'),
        ],
    ];

    $birth_of_a_grandchild = [
        'BIRT' => [
            'M' => I18N::translate('Birth of a grandson'),
            'F' => I18N::translate('Birth of a granddaughter'),
            'U' => I18N::translate('Birth of a grandchild'),
        ],
        'CHR' => [
            'M' => I18N::translate('Christening of a grandson'),
            'F' => I18N::translate('Christening of a granddaughter'),
            'U' => I18N::translate('Christening of a grandchild'),
        ],
        'BAPM' => [
            'M' => I18N::translate('Baptism of a grandson'),
            'F' => I18N::translate('Baptism of a granddaughter'),
            'U' => I18N::translate('Baptism of a grandchild'),
        ],
        'ADOP' => [
            'M' => I18N::translate('Adoption of a grandson'),
            'F' => I18N::translate('Adoption of a granddaughter'),
            'U' => I18N::translate('Adoption of a grandchild'),
        ],
    ];

    $birth_of_a_grandchild1 = [
        'BIRT' => [
            'M' => I18N::translateContext('daughter???s son', 'Birth of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Birth of a granddaughter'),
            'U' => I18N::translate('Birth of a grandchild'),
        ],
        'CHR' => [
            'M' => I18N::translateContext('daughter???s son', 'Christening of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Christening of a granddaughter'),
            'U' => I18N::translate('Christening of a grandchild'),
        ],
        'BAPM' => [
            'M' => I18N::translateContext('daughter???s son', 'Baptism of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Baptism of a granddaughter'),
            'U' => I18N::translate('Baptism of a grandchild'),
        ],
        'ADOP' => [
            'M' => I18N::translateContext('daughter???s son', 'Adoption of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Adoption of a granddaughter'),
            'U' => I18N::translate('Adoption of a grandchild'),
        ],
    ];

    $birth_of_a_grandchild2 = [
        'BIRT' => [
            'M' => I18N::translateContext('son???s son', 'Birth of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Birth of a granddaughter'),
            'U' => I18N::translate('Birth of a grandchild'),
        ],
        'CHR' => [
            'M' => I18N::translateContext('son???s son', 'Christening of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Christening of a granddaughter'),
            'U' => I18N::translate('Christening of a grandchild'),
        ],
        'BAPM' => [
            'M' => I18N::translateContext('son???s son', 'Baptism of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Baptism of a granddaughter'),
            'U' => I18N::translate('Baptism of a grandchild'),
        ],
        'ADOP' => [
            'M' => I18N::translateContext('son???s son', 'Adoption of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Adoption of a granddaughter'),
            'U' => I18N::translate('Adoption of a grandchild'),
        ],
    ];

    $death_of_a_child = [
        'DEAT' => [
            'M' => I18N::translate('Death of a son'),
            'F' => I18N::translate('Death of a daughter'),
            'U' => I18N::translate('Death of a child'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a son'),
            'F' => I18N::translate('Burial of a daughter'),
            'U' => I18N::translate('Burial of a child'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a son'),
            'F' => I18N::translate('Cremation of a daughter'),
            'U' => I18N::translate('Cremation of a child'),
        ],
    ];

    $death_of_a_sibling = [
        'DEAT' => [
            'M' => I18N::translate('Death of a brother'),
            'F' => I18N::translate('Death of a sister'),
            'U' => I18N::translate('Death of a sibling'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a brother'),
            'F' => I18N::translate('Burial of a sister'),
            'U' => I18N::translate('Burial of a sibling'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a brother'),
            'F' => I18N::translate('Cremation of a sister'),
            'U' => I18N::translate('Cremation of a sibling'),
        ],
    ];

    $death_of_a_half_sibling = [
        'DEAT' => [
            'M' => I18N::translate('Death of a half-brother'),
            'F' => I18N::translate('Death of a half-sister'),
            'U' => I18N::translate('Death of a half-sibling'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a half-brother'),
            'F' => I18N::translate('Burial of a half-sister'),
            'U' => I18N::translate('Burial of a half-sibling'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a half-brother'),
            'F' => I18N::translate('Cremation of a half-sister'),
            'U' => I18N::translate('Cremation of a half-sibling'),
        ],
    ];

    $death_of_a_grandchild = [
        'DEAT' => [
            'M' => I18N::translate('Death of a grandson'),
            'F' => I18N::translate('Death of a granddaughter'),
            'U' => I18N::translate('Death of a grandchild'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a grandson'),
            'F' => I18N::translate('Burial of a granddaughter'),
            'U' => I18N::translate('Burial of a grandchild'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a grandson'),
            'F' => I18N::translate('Cremation of a granddaughter'),
            'U' => I18N::translate('Baptism of a grandchild'),
        ],
    ];

    $death_of_a_grandchild1 = [
        'DEAT' => [
            'M' => I18N::translateContext('daughter???s son', 'Death of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Death of a granddaughter'),
            'U' => I18N::translate('Death of a grandchild'),
        ],
        'BURI' => [
            'M' => I18N::translateContext('daughter???s son', 'Burial of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Burial of a granddaughter'),
            'U' => I18N::translate('Burial of a grandchild'),
        ],
        'CREM' => [
            'M' => I18N::translateContext('daughter???s son', 'Cremation of a grandson'),
            'F' => I18N::translateContext('daughter???s daughter', 'Cremation of a granddaughter'),
            'U' => I18N::translate('Baptism of a grandchild'),
        ],
    ];

    $death_of_a_grandchild2 = [
        'DEAT' => [
            'M' => I18N::translateContext('son???s son', 'Death of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Death of a granddaughter'),
            'U' => I18N::translate('Death of a grandchild'),
        ],
        'BURI' => [
            'M' => I18N::translateContext('son???s son', 'Burial of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Burial of a granddaughter'),
            'U' => I18N::translate('Burial of a grandchild'),
        ],
        'CREM' => [
            'M' => I18N::translateContext('son???s son', 'Cremation of a grandson'),
            'F' => I18N::translateContext('son???s daughter', 'Cremation of a granddaughter'),
            'U' => I18N::translate('Cremation of a grandchild'),
        ],
    ];

    $marriage_of_a_child = [
        'M' => I18N::translate('Marriage of a son'),
        'F' => I18N::translate('Marriage of a daughter'),
        'U' => I18N::translate('Marriage of a child'),
    ];

    $marriage_of_a_grandchild = [
        'M' => I18N::translate('Marriage of a grandson'),
        'F' => I18N::translate('Marriage of a granddaughter'),
        'U' => I18N::translate('Marriage of a grandchild'),
    ];

    $marriage_of_a_grandchild1 = [
        'M' => I18N::translateContext('daughter???s son', 'Marriage of a grandson'),
        'F' => I18N::translateContext('daughter???s daughter', 'Marriage of a granddaughter'),
        'U' => I18N::translate('Marriage of a grandchild'),
    ];

    $marriage_of_a_grandchild2 = [
        'M' => I18N::translateContext('son???s son', 'Marriage of a grandson'),
        'F' => I18N::translateContext('son???s daughter', 'Marriage of a granddaughter'),
        'U' => I18N::translate('Marriage of a grandchild'),
    ];

    $marriage_of_a_sibling = [
        'M' => I18N::translate('Marriage of a brother'),
        'F' => I18N::translate('Marriage of a sister'),
        'U' => I18N::translate('Marriage of a sibling'),
    ];

    $marriage_of_a_half_sibling = [
        'M' => I18N::translate('Marriage of a half-brother'),
        'F' => I18N::translate('Marriage of a half-sister'),
        'U' => I18N::translate('Marriage of a half-sibling'),
    ];

    $facts = [];

    // Deal with recursion.
    switch ($option) {
      case '_CHIL':
        // Add grandchildren
        foreach ($family->children() as $child) {
          foreach ($child->spouseFamilies() as $cfamily) {
            switch ($child->sex()) {
              case 'M':
                foreach ($this->childFacts($person, $cfamily, '_GCHI', 'son', $min_date, $max_date) as $fact) {
                  $facts[] = $fact;
                }
                break;
              case 'F':
                foreach ($this->childFacts($person, $cfamily, '_GCHI', 'dau', $min_date, $max_date) as $fact) {
                  $facts[] = $fact;
                }
                break;
              default:
                foreach ($this->childFacts($person, $cfamily, '_GCHI', 'chi', $min_date, $max_date) as $fact) {
                  $facts[] = $fact;
                }
                break;
            }
          }
        }
        break;
    }

    // For each child in the family
    foreach ($family->children() as $child) {
      if ($child->xref() === $person->xref()) {
        // We are not our own sibling!
        continue;
      }
      // add child???s birth
      if (str_contains($SHOW_RELATIVES_EVENTS, '_BIRT' . str_replace('_HSIB', '_SIBL', $option))) {
        foreach ($child->facts(['BIRT', 'CHR', 'BAPM', 'ADOP']) as $fact) {
          // Always show _BIRT_CHIL, even if the dates are not known
          if ($option === '_CHIL' || $this->includeFact($fact, $min_date, $max_date)) {
            switch ($option) {
              case '_GCHI':
                switch ($relation) {
                  case 'dau':
                    $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild1[$fact->getTag()][$fact->record()->sex()]);
                    break;
                  case 'son':
                    $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild2[$fact->getTag()][$fact->record()->sex()]);
                    break;
                  case 'chil':
                    $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild[$fact->getTag()][$fact->record()->sex()]);
                    break;
                }
                break;
              case '_SIBL':
                $facts[] = $this->convertEvent($fact, $birth_of_a_sibling[$fact->getTag()][$fact->record()->sex()]);
                break;
              case '_HSIB':
                $facts[] = $this->convertEvent($fact, $birth_of_a_half_sibling[$fact->getTag()][$fact->record()->sex()]);
                break;
              case '_CHIL':
                $facts[] = $this->convertEvent($fact, $birth_of_a_child[$fact->getTag()][$fact->record()->sex()]);
                break;
            }
          }
        }
      }
      // add child???s death
      if (str_contains($SHOW_RELATIVES_EVENTS, '_DEAT' . str_replace('_HSIB', '_SIBL', $option))) {
        foreach ($child->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
          if ($this->includeFact($fact, $min_date, $max_date)) {
            switch ($option) {
              case '_GCHI':
                switch ($relation) {
                  case 'dau':
                    $facts[] = $this->convertEvent($fact, $death_of_a_grandchild1[$fact->getTag()][$fact->record()->sex()]);
                    break;
                  case 'son':
                    $facts[] = $this->convertEvent($fact, $death_of_a_grandchild2[$fact->getTag()][$fact->record()->sex()]);
                    break;
                  case 'chi':
                    $facts[] = $this->convertEvent($fact, $death_of_a_grandchild[$fact->getTag()][$fact->record()->sex()]);
                    break;
                }
                break;
              case '_SIBL':
                $facts[] = $this->convertEvent($fact, $death_of_a_sibling[$fact->getTag()][$fact->record()->sex()]);
                break;
              case '_HSIB':
                $facts[] = $this->convertEvent($fact, $death_of_a_half_sibling[$fact->getTag()][$fact->record()->sex()]);
                break;
              case '_CHIL':
                $facts[] = $this->convertEvent($fact, $death_of_a_child[$fact->getTag()][$fact->record()->sex()]);
                break;
            }
          }
        }
      }

      // add child???s marriage
      if (str_contains($SHOW_RELATIVES_EVENTS, '_MARR' . str_replace('_HSIB', '_SIBL', $option))) {
        foreach ($child->spouseFamilies() as $sfamily) {
          foreach ($sfamily->facts(['MARR']) as $fact) {
            if ($this->includeFact($fact, $min_date, $max_date)) {
              switch ($option) {
                case '_GCHI':
                  switch ($relation) {
                    case 'dau':
                      $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild1['F']);
                      break;
                    case 'son':
                      $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild2['M']);
                      break;
                    case 'chi':
                      $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild['U']);
                      break;
                  }
                  break;
                case '_SIBL':
                  $facts[] = $this->convertEvent($fact, $marriage_of_a_sibling['U']);
                  break;
                case '_HSIB':
                  $facts[] = $this->convertEvent($fact, $marriage_of_a_half_sibling['U']);
                  break;
                case '_CHIL':
                  $facts[] = $this->convertEvent($fact, $marriage_of_a_child['U']);
                  break;
              }
            }
          }
        }
      }
    }

    return $facts;
  }

  /**
   * Get the events of parents and grandparents.
   *
   * @param Individual $person
   * @param int        $sosa
   * @param Date       $min_date
   * @param Date       $max_date
   *
   * @return Fact[]
   */
    private function parentFacts(Individual $person, int $sosa, Date $min_date, Date $max_date): array
    {
    $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

    $death_of_a_parent = [
        'DEAT' => [
            'M' => I18N::translate('Death of a father'),
            'F' => I18N::translate('Death of a mother'),
            'U' => I18N::translate('Death of a parent'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a father'),
            'F' => I18N::translate('Burial of a mother'),
            'U' => I18N::translate('Burial of a parent'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a father'),
            'F' => I18N::translate('Cremation of a mother'),
            'U' => I18N::translate('Cremation of a parent'),
        ],
    ];

    $death_of_a_grandparent = [
        'DEAT' => [
            'M' => I18N::translate('Death of a grandfather'),
            'F' => I18N::translate('Death of a grandmother'),
            'U' => I18N::translate('Death of a grandparent'),
        ],
        'BURI' => [
            'M' => I18N::translate('Burial of a grandfather'),
            'F' => I18N::translate('Burial of a grandmother'),
            'U' => I18N::translate('Burial of a grandparent'),
        ],
        'CREM' => [
            'M' => I18N::translate('Cremation of a grandfather'),
            'F' => I18N::translate('Cremation of a grandmother'),
                'U' => I18N::translate('Cremation of a grandparent'),
        ],
    ];

    $death_of_a_maternal_grandparent = [
        'DEAT' => [
                'M' => I18N::translate('Death of a maternal grandfather'),
                'F' => I18N::translate('Death of a maternal grandmother'),
            'U' => I18N::translate('Death of a grandparent'),
        ],
        'BURI' => [
                'M' => I18N::translate('Burial of a maternal grandfather'),
                'F' => I18N::translate('Burial of a maternal grandmother'),
            'U' => I18N::translate('Burial of a grandparent'),
        ],
        'CREM' => [
                'M' => I18N::translate('Cremation of a maternal grandfather'),
                'F' => I18N::translate('Cremation of a maternal grandmother'),
                'U' => I18N::translate('Cremation of a grandparent'),
        ],
    ];

    $death_of_a_paternal_grandparent = [
        'DEAT' => [
                'M' => I18N::translate('Death of a paternal grandfather'),
                'F' => I18N::translate('Death of a paternal grandmother'),
            'U' => I18N::translate('Death of a grandparent'),
        ],
        'BURI' => [
                'M' => I18N::translate('Burial of a paternal grandfather'),
                'F' => I18N::translate('Burial of a paternal grandmother'),
            'U' => I18N::translate('Burial of a grandparent'),
        ],
        'CREM' => [
                'M' => I18N::translate('Cremation of a paternal grandfather'),
                'F' => I18N::translate('Cremation of a paternal grandmother'),
            'U' => I18N::translate('Cremation of a grandparent'),
        ],
    ];

    $marriage_of_a_parent = [
        'M' => I18N::translate('Marriage of a father'),
        'F' => I18N::translate('Marriage of a mother'),
        'U' => I18N::translate('Marriage of a parent'),
    ];

    $facts = [];

        if ($sosa === 1) {
      foreach ($person->childFamilies() as $family) {
        // Add siblings
        foreach ($this->childFacts($person, $family, '_SIBL', '', $min_date, $max_date) as $fact) {
          $facts[] = $fact;
        }
        foreach ($family->spouses() as $spouse) {
          foreach ($spouse->spouseFamilies() as $sfamily) {
            if ($family !== $sfamily) {
              // Add half-siblings
              foreach ($this->childFacts($person, $sfamily, '_HSIB', '', $min_date, $max_date) as $fact) {
                $facts[] = $fact;
              }
            }
          }
          // Add grandparents
          foreach ($this->parentFacts($spouse, $spouse->sex() === 'F' ? 3 : 2, $min_date, $max_date) as $fact) {
            $facts[] = $fact;
          }
        }
      }

            if (str_contains($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
        // add father/mother marriages
        foreach ($person->childFamilies() as $sfamily) {
          foreach ($sfamily->facts(['MARR']) as $fact) {
            if ($this->includeFact($fact, $min_date, $max_date)) {
              // marriage of parents (to each other)
              $facts[] = $this->convertEvent($fact, I18N::translate('Marriage of parents'));
            }
          }
        }
        foreach ($person->childStepFamilies() as $sfamily) {
          foreach ($sfamily->facts(['MARR']) as $fact) {
            if ($this->includeFact($fact, $min_date, $max_date)) {
              // marriage of a parent (to another spouse)
              $facts[] = $this->convertEvent($fact, $marriage_of_a_parent['U']);
            }
          }
        }
      }
    }

    foreach ($person->childFamilies() as $family) {
      foreach ($family->spouses() as $parent) {
        if (str_contains($SHOW_RELATIVES_EVENTS, '_DEAT' . ($sosa === 1 ? '_PARE' : '_GPAR'))) {
          foreach ($parent->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
            // Show death of parent when it happened prior to birth
            if ($sosa === 1 && Date::compare($fact->date(), $min_date) < 0 || $this->includeFact($fact, $min_date, $max_date)) {
              switch ($sosa) {
                case 1:
                  $facts[] = $this->convertEvent($fact, $death_of_a_parent[$fact->getTag()][$fact->record()->sex()]);
                  break;
                case 2:
                case 3:
                  switch ($person->sex()) {
                    case 'M':
                      $facts[] = $this->convertEvent($fact, $death_of_a_paternal_grandparent[$fact->getTag()][$fact->record()->sex()]);
                      break;
                    case 'F':
                      $facts[] = $this->convertEvent($fact, $death_of_a_maternal_grandparent[$fact->getTag()][$fact->record()->sex()]);
                      break;
                    default:
                      $facts[] = $this->convertEvent($fact, $death_of_a_grandparent[$fact->getTag()][$fact->record()->sex()]);
                      break;
                  }
              }
            }
          }
        }
      }
    }

    return $facts;
  }

  /**
   * Get any historical events.
   *
   * @param Individual $individual
   *
   * @return Fact[]
   */
    private function historicalFacts(Individual $individual): array
    {
    return $this->module_service->findByInterface(ModuleHistoricEventsInterface::class)
                    ->map(static function (ModuleHistoricEventsInterface $module) use ($individual): Collection {
                      return $module->historicEventsForIndividual($individual);
                    })
                    ->flatten()
                    ->all();
  }

  //[RC] OverrideHook

  /**
   * 
   * @param Fact $fact
   * @return boolean
   */
  protected function filterAssociateFact(Fact $fact) {
    return true;
  }

  //[RC] ADDED - should be in Fact class
  public static function getAttributes($fact, $tag) {
    preg_match_all('/\n2 (?:' . $tag . ') ?(.*(?:(?:\n3 CONT ?.*)*)*)/', $fact->gedcom(), $matches);
    $attributes = array();
    foreach ($matches[1] as $match) {
      $attributes[] = preg_replace("/\n3 CONT ?/", "\n", $match);
    }

    return $attributes;
  }

  /**
   * Get the events of associates.
   *
   * @param Individual $person
   *
   * @return Fact[]
   */
  //[RC] changed visibility
  protected function associateFacts(Individual $person): array {
    $facts = [];

    /** @var Individual[] $associates */
    $asso1 = $person->linkedIndividuals('ASSO');
    $asso2 = $person->linkedIndividuals('_ASSO');
    $asso3 = $person->linkedFamilies('ASSO');
    $asso4 = $person->linkedFamilies('_ASSO');

    $associates = $asso1->merge($asso2)->merge($asso3)->merge($asso4);
    
    //#17: remove duplicates
    $associates = $associates->unique();

    foreach ($associates as $associate) {
      foreach ($associate->facts() as $fact) {
        //[RC] addded
        if (!$this->filterAssociateFact($fact)) {
          continue;
        }

        //webtrees 2.x fix for #1192
        //we cannot use it because we require the per-asso relas!
        /*
          if (preg_match('/\n\d _?ASSO @' . $person->xref() . '@/', $fact->gedcom())) {

          }
         */

        //[RC] PATCHED: fix for #1192
        //plus extension for RELA
        
        //#17: also handle 1 ASSO
        preg_match_all('/^1 ASSO @(' . Gedcom::REGEX_XREF . ')@((\n[2-9].*)*)/', $fact->gedcom(), $arecs1, PREG_SET_ORDER);
        
        preg_match_all('/\n2 _?ASSO @(.*)@((\n[3].*)*)/', $fact->gedcom(), $arecs2, PREG_SET_ORDER);
        $arecs = array_merge($arecs1, $arecs2);
        
        foreach ($arecs as $arec) {
          $xref = $arec[1];
          $rela = $arec[2];

          if ($xref === $person->xref()) {
            // Extract the important details from the fact
            $factrec = '1 ' . $fact->getTag();
            if (preg_match('/\n2 DATE .*/', $fact->gedcom(), $match)) {
              $factrec .= $match[0];
            }
            if (preg_match('/\n2 PLAC .*/', $fact->gedcom(), $match)) {
              $factrec .= $match[0];
            }
            //[RC] adjusted for Issue #7
            if (preg_match('/\n2 TYPE .*/', $fact->gedcom(), $match)) {
              $factrec .= $match[0];
            }
            if ($associate instanceof Family) {
              foreach ($associate->spouses() as $spouse) {
                $factrec .= "\n2 _ASSO @" . $spouse->xref() . '@';

                //[RC] extension for RELA
                // Is there a "RELA" tag (code adjusted from elsewhere - note though that strictly according to the Gedcom spec, RELA is mandatory!)
                if (preg_match('/\n3 RELA (.+)/', $rela)) {
                  //preserve RELA
                  $factrec .= $rela;
                } else {
                  //skip
                }
              }
            } else {
              $factrec .= "\n2 _ASSO @" . $associate->xref() . '@';

              //[RC] extension for RELA
              // Is there a "RELA" tag (code adjusted from elsewhere - note though that strictly according to the Gedcom spec, RELA is mandatory!)
              if (preg_match('/\n\d RELA (.+)/', $rela)) {
                //preserve RELA
                $factrec .= $rela;
              } else {
                //skip
              }
            }
            $facts[] = new Fact($factrec, $associate, 'asso');
          }
        }
      }
    }

    return $facts;
  }

  /**
   * This module handles the following facts - so don't show them on the "Facts and events" tab.
   *
   * @return Collection<string>
   */
  public function supportedFacts(): Collection {
    // We don't actually displaye these facts, but they are displayed
    // outside the tabs/sidebar systems. This just forces them to be excluded here.
    return new Collection(['NAME', 'SEX', 'OBJE']);
  }

  //[RC] added
  //OverrideHook
  protected function additionalFacts(Individual $person) {
    return array();
  }

  //[RC] added/ refactored
  //OverrideHook
  protected function getToggleableFactsCategories($show_relatives_facts, $has_historical_facts) {
    $categories = [];

    /* [RC] note: this is problematic wrt asso events, which we still may want to show */
    if ($show_relatives_facts) {
      $categories[] = new ToggleableFactsCategory(
              'show-relatives-facts',
              '.wt-relation-fact',
              I18N::translate('Events of close relatives'));
    }

    if ($has_historical_facts) {
      $categories[] = new ToggleableFactsCategory(
              'show-historical-facts',
              '.wt-historic-fact',
              I18N::translate('Historic events'));
    }

    return $categories;
  }

  //[RC] override hooks

  protected function getOutputBeforeTab(Individual $person) {
    return GenericViewElement::createEmpty();
  }

  protected function getOutputAfterTab(Individual $person) {
    return GenericViewElement::createEmpty();
  }

  protected function getOutputInDescriptionBox(Individual $person) {
    return GenericViewElement::createEmpty();
  }

  protected function getOutputAfterDescriptionBox(Individual $person) {
    return GenericViewElement::createEmpty();
  }

}
