<?php

namespace Cissee\WebtreesExt\Contracts;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Vesta\Hook\HookInterfaces\IndividualFactsTabExtenderInterface;
use Vesta\Hook\HookInterfaces\IndividualFactsTabExtenderUtils;
use Vesta\Model\PlaceStructure;
use Vesta\Model\VestalRequest;
use Vesta\Model\VestalResponse;
use Vesta\VestaUtils;
use function view;

class FunctionsVestals {
    
    protected ModuleInterface $module;
    protected string $vestalsActionUrl;
    protected bool $useVestals;
    protected bool $hideCoordinates;

    function __construct(
            ModuleInterface $module,
            string $vestalsActionUrl,
            bool $useVestals,
            bool $hideCoordinates) {

        $this->module = $module;
        $this->vestalsActionUrl = $vestalsActionUrl;
        $this->useVestals = $useVestals;
        $this->hideCoordinates = $hideCoordinates;
    }  

    public function vestalsActionUrl(): string {
        return $this->vestalsActionUrl;
    }
  
    public function vestalsForFactPlace(
        Fact $event): array {
    
        $ps = PlaceStructure::fromFact($event);
        if ($ps === null) {
          return [];
        }

        return $this->vestalsForPlaceNameAndSubRecords($ps);
    }
  
    public function vestalsForPlaceNameAndSubRecords(
        PlaceStructure $ps): array {
    
        if (!$this->useVestals) {
          return [];
        }

        $requests = [];

        $key = md5(json_encode($ps));
        $requests[$key.'_vestalMapCoordinates'] = new VestalRequest('vestalMapCoordinates', $ps);
        $requests[$key.'_vestalBeforePlace'] = new VestalRequest('vestalBeforePlace', $ps);
        $requests[$key.'_vestalAfterMap'] = new VestalRequest('vestalAfterMap', $ps);
        $requests[$key.'_vestalAfterNotes'] = new VestalRequest('vestalAfterNotes', $ps);

        return $requests;
    }
  
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
      
    public function vestalBeforePlace(
        PlaceStructure $ps): VestalResponse {
    
        $factPlaceAdditions = IndividualFactsTabExtenderUtils::accessibleModules($this->module, $ps->getTree(), Auth::user())
                ->map(function (IndividualFactsTabExtenderInterface $module) use ($ps) {
                  return $module->factPlaceAdditionsBeforePlace($ps);
                })
                ->filter() //filter null values
                ->toArray();

        $html1 = '';        
        foreach ($factPlaceAdditions as $factPlaceAddition) {
          $html1 .= $factPlaceAddition;
        }

        $key = md5(json_encode($ps));
        return new VestalResponse($key.'_vestalBeforePlace', $html1);
    }
  
    public function vestalAfterMap(
        PlaceStructure $ps): VestalResponse {
    
        $factPlaceAdditions = IndividualFactsTabExtenderUtils::accessibleModules($this->module, $ps->getTree(), Auth::user())
                ->map(function (IndividualFactsTabExtenderInterface $module) use ($ps) {
                  return $module->factPlaceAdditionsAfterMap($ps);
                })
                ->filter() //filter null values
                ->toArray();

        $html = '';        
        foreach ($factPlaceAdditions as $factPlaceAddition) {
          $html .= $factPlaceAddition;
        }
   
        $key = md5(json_encode($ps));
        return new VestalResponse($key.'_vestalAfterMap', $html);
    }
  
    public function vestalAfterNotes(
        PlaceStructure $ps): VestalResponse {
    
        $factPlaceAdditions = IndividualFactsTabExtenderUtils::accessibleModules($this->module, $ps->getTree(), Auth::user())
                ->map(function (IndividualFactsTabExtenderInterface $module) use ($ps) {
                  return $module->factPlaceAdditionsAfterNotes($ps);
                })
                ->filter() //filter null values
                ->toArray();

        $html = '';        
        foreach ($factPlaceAdditions as $factPlaceAddition) {
          $html .= $factPlaceAddition;
        }

        $key = md5(json_encode($ps));
        return new VestalResponse($key.'_vestalAfterNotes', $html);
    }

    public function vestalMapCoordinates(
        PlaceStructure $ps): VestalResponse {
        
        $key = md5(json_encode($ps));
        
        $html = view(VestaUtils::vestaViewsNamespace() . '::fact-place-coordinates', [
                        'ps' => $ps, 
                        'module' => $this->module,
                        'hideCoordinates' => $this->hideCoordinates]);
            
        return new VestalResponse($key.'_vestalMapCoordinates', $html);
    }
}
