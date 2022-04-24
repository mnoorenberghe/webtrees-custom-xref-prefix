<?php

declare(strict_types=1);

namespace Cissee\WebtreesExt\Http\RequestHandlers;

use Cissee\Webtrees\Module\SharedPlaces\SharedPlacesModule_20;
use Cissee\WebtreesExt\Functions\FunctionsPrintExtHelpLink;
use Cissee\WebtreesExt\SharedPlace;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vesta\Hook\HookInterfaces\FunctionsPlaceUtils;
use Vesta\Model\PlaceStructure;
use function redirect;

//cf SourcePage
/**
 * Show a shared place's page.
 */
class SharedPlacePage_20 implements RequestHandlerInterface {

    use ViewResponseTrait;
    
    // Show the shared place's facts in this order:
    private const FACT_ORDER = [
        1 => 'NAME',
        2 => 'TYPE',
        3 => '_LOC',
        4 => 'MAP',
        5 => '_GOV',
        'ABBR',
        'AUTH',
        'DATA',
        'PUBL',
        'TEXT',
        'REPO',
        'NOTE',
        'OBJE',
        'REFN',
        'RIN',
        '_UID',
        'CHAN',
        'RESN',
    ];
  
    protected $module;
    
    /** @var ClipboardService */
    private $clipboard_service;
    
    public function __construct(
            ModuleService $moduleService, 
            ClipboardService $clipboard_service) {
        
        //access level irrelevant here: there is no way to configure an access level for this specific functionality
        //(it's not a list, chart, etc. - we'd have to define it specifically)
        $this->module = $moduleService->findByInterface(SharedPlacesModule_20::class, false)->first();
        
        //otherwise we wouldn't even get here (router redirects)
        assert ($this->module instanceof SharedPlacesModule_20);
        
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        $sharedPlace = Registry::locationFactory()->make($xref, $tree);

        //we don't need a specific method here, 
        //if we ever refactor this:
        //use SharedPlaceNotFoundException! 
        Auth::checkLocationAccess($sharedPlace, false);

        // Redirect to correct xref/slug
        if ($sharedPlace->xref() !== $xref || $request->getAttribute('slug') !== $sharedPlace->slug()) {
            return redirect($sharedPlace->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }
        
      $canonical = $sharedPlace->primaryPlace()->gedcomName();
      
      $hierarchyHtml = '';
      
      //what was the point of this? summary shows same data!
      /*
      $mainForDisplay = $sharedPlace->fullName();
      $canonicalForDisplay = $sharedPlace->primaryPlace()->fullName();
      
      if ($mainForDisplay !== $canonicalForDisplay) {
        $hierarchyHtml = '<tr class=""><th scope="row">' . I18N::translate('Shared place hierarchy') . '</th><td class="">' . $canonicalForDisplay . '</td></tr>';
      }
      */
      
      //summary (with any additional data such as GOV data, map links etc),
      //if there is a module that provides this summary
      $summaryHtml = '';
      if (!empty($sharedPlace->namesNN())) {
        $refYear = intVal($this->module->getPreference('REF_YEAR', ''));
        if ($refYear) {
          $ps = PlaceStructure::fromNameAndLocWithYear($refYear, $canonical, $sharedPlace->xref(), $sharedPlace->tree(), 0, $sharedPlace);
        } else {
          $ps = PlaceStructure::fromNameAndLocNow($canonical, $sharedPlace->xref(), $sharedPlace->tree(), 0, $sharedPlace);
        }
        
        if ($ps !== null) {
          $summaryGve = FunctionsPlaceUtils::plac2html($this->module, $ps);

          //if ($summaryHtml !== '') {
            $summaryHtml = '<tr class=""><th scope="row">' . I18N::translate('Summary') . FunctionsPrintExtHelpLink::helpLink($this->module->name(), 'Summary') . '</th><td class="">' . $summaryGve->getMain() . '</td></tr>';
            //TODO: handle getScript()!
          //}          
        }
      }
      
      return $this->viewResponse($this->module->name() . '::shared-place-page_20', [
                    'module' => $this->module,
                    'moduleName' => $this->module->name(),
                    'clipboard_facts'  => $this->clipboard_service->pastableFacts($sharedPlace, new Collection()),
                    'hierarchyHtml' => $hierarchyHtml,
                    'summaryHtml' => $summaryHtml,
                    'facts' => $this->facts($sharedPlace),
                    'individuals' => $sharedPlace->linkedIndividuals('_LOC'),
                    'families' => $sharedPlace->linkedFamilies('_LOC'),
                    'llSharedPlaces' => $sharedPlace->linkedLocations('_LOC'),
                    'sharedPlace' => $sharedPlace,
                    'meta_robots' => 'index,follow',
                    'title' => $sharedPlace->fullName(),
                    'tree' => $tree
        ]);
    }
    
    private function facts(SharedPlace $record): Collection {
      
      $factsArray = $record->facts()->toArray();
      
      $facts = $record->facts()
              ->sort(function (Fact $x, Fact $y) use ($factsArray): int {
        $sort_x = array_search($x->getTag(), self::FACT_ORDER) ?: PHP_INT_MAX;
        $sort_y = array_search($y->getTag(), self::FACT_ORDER) ?: PHP_INT_MAX;

        $cmp = $sort_x <=> $sort_y;
        if ($cmp !== 0) {
          return $cmp;
        }
        
        //fallback to original order within gedcom (e.g. for multiple names)
        return array_search($x, $factsArray) <=> array_search($y, $factsArray);
      });
      
      return $facts;
    }
}
