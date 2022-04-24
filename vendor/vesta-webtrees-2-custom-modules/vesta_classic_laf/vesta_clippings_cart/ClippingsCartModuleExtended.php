<?php

namespace Cissee\Webtrees\Module\ClippingsCart;

use Cissee\WebtreesExt\Module\ModuleMetaInterface;
use Cissee\WebtreesExt\Module\ModuleMetaTrait;
use Cissee\WebtreesExt\MoreI18N;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\ClippingsCartModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleConfigTrait;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Vesta\Hook\HookInterfaces\ClippingsCartAddToCartInterface;
use Vesta\Hook\HookInterfaces\FunctionsClippingsCartUtils;
use Vesta\VestaModuleTrait;
use function redirect;

class ClippingsCartModuleExtended extends ClippingsCartModule implements
ModuleCustomInterface, ModuleMetaInterface, ModuleConfigInterface, ModuleMenuInterface, ClippingsCartAddToCartInterface {

    use ModuleCustomTrait,
        ModuleMetaTrait,
        ModuleConfigTrait,
        VestaModuleTrait {
        VestaModuleTrait::title as trait_title;
        VestaModuleTrait::customTranslations insteadof ModuleCustomTrait;
        VestaModuleTrait::getAssetAction insteadof ModuleCustomTrait;
        VestaModuleTrait::assetUrl insteadof ModuleCustomTrait;
        VestaModuleTrait::getConfigLink insteadof ModuleConfigTrait;
        ModuleMetaTrait::customModuleVersion insteadof ModuleCustomTrait;
        ModuleMetaTrait::customModuleLatestVersion insteadof ModuleCustomTrait;
    }

    use ClippingsCartModuleTrait;

    public function __construct(
        GedcomExportService $gedcom_export_service,
        LinkedRecordService $linked_record_service,
        ResponseFactoryInterface $response_factory,
        StreamFactoryInterface $stream_factory) {

        parent::__construct(
            $gedcom_export_service,
            $linked_record_service,
            $response_factory,
            $stream_factory);
    }

    public function customModuleAuthorName(): string {
        return 'Richard Cissée';
    }

    public function customModuleMetaDatasJson(): string {
        return file_get_contents(__DIR__ . '/metadata.json');
    }

    public function customModuleLatestMetaDatasJsonUrl(): string {
        return 'https://raw.githubusercontent.com/vesta-webtrees-2-custom-modules/vesta_clippings_cart/master/metadata.json';
    }

    public function customModuleSupportUrl(): string {
        return 'https://cissee.de';
    }

    public function resourcesFolder(): string {
        return __DIR__ . '/resources/';
    }

    /**
     * Bootstrap the module
     */
    public function onBoot(): void {
        $this->flashWhatsNew('\Cissee\Webtrees\Module\ClippingsCart\WhatsNew', 1);
    }

    protected function menuTitle(): string {
        return $this->getMenuTitle(MoreI18N::xlate("Clippings cart"));
    }

    /** @var bool */
    protected $switchTitle = false;

    public function title(): string {
        //usually trait_title(), but for the method called via parent::getMenu we need menuTitle()!
        if ($this->switchTitle) {
            return $this->menuTitle();
        }
        return $this->trait_title();
    }

    public function getMenu(Tree $tree): ?Menu {
        $this->switchTitle = true;
        $ret = parent::getMenu($tree);
        $this->switchTitle = false;
        return $ret;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAddLocationAction(ServerRequestInterface $request): ResponseInterface {
        
        $tree = Validator::attributes($request)->tree();

        $xref = $request->getQueryParams()['xref'] ?? '';

        $location = Registry::locationFactory()->make($xref, $tree);
        $location = Auth::checkLocationAccess($location);
        $name = $location->fullName();

        $options = [
            'record' => $name,
        ];

        $additionalOptions = $this->getAddLocationActionAdditionalOptions($location);
        $options = array_merge($options, $additionalOptions);

        $title = MoreI18N::xlate('Add %s to the clippings cart', $name);

        return $this->viewResponse('modules/clippings/add-options', [
                'options' => $options,
                'record' => $location,
                'title' => $title,
                'tree' => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddLocationAction(ServerRequestInterface $request): ResponseInterface {
        $tree = Validator::attributes($request)->tree();

        $params = (array) $request->getParsedBody();

        $xref = $params['xref'] ?? '';
        $option = $params['option'] ?? '';

        $location = Registry::locationFactory()->make($xref, $tree);
        $location = Auth::checkLocationAccess($location);

        $this->addLocationToCart($location);

        $this->postAddLocationActionHandleOption($location, $option);

        return redirect($location->url());
    }

    /**
     * @param GedcomRecord $record
     */
    protected function addLocationLinksToCart(GedcomRecord $record): void {
        preg_match_all('/\n\d _LOC @(' . Gedcom::REGEX_XREF . ')@/', $record->gedcom(), $matches);

        foreach ($matches[1] as $xref) {
            $location = Registry::locationFactory()->make($xref, $record->tree());

            if ($location instanceof Location && $location->canShow()) {
                $this->addLocationToCart($location);
            }
        }

        //added
        foreach ($this->getIndirectLocations($record) as $xref) {
            $location = Registry::locationFactory()->make($xref, $record->tree());

            if ($location instanceof Location && $location->canShow()) {
                $this->addLocationToCart($location);
            }
        }
    }

    protected function getAddLocationActionAdditionalOptions(Location $location): array {
        return FunctionsClippingsCartUtils::getAddLocationActionAdditionalOptions($location);
    }

    //e.g. _LOC with linked INDI/FAM (not possible in standard clippings cart)
    protected function postAddLocationActionHandleOption(Location $location, string $option) {
        return FunctionsClippingsCartUtils::postAddLocationActionHandleOption($this, $location, $option);
    }

    //e.g. INDI/FAM to _LOC, indirectly (not possible in standard clippings cart)
    protected function getIndirectLocations(GedcomRecord $record): Collection {
        return FunctionsClippingsCartUtils::getIndirectLocations($record);
    }

    public function doAddIndividualToCart(Individual $individual): void {
        $this->addIndividualToCart($individual);
    }

    public function doAddFamilyToCart(Family $family): void {
        $this->addFamilyToCart($family);
    }

    public function doAddFamilyAndChildrenToCart(Family $family): void {
        $this->addFamilyAndChildrenToCart($family);
    }

}
