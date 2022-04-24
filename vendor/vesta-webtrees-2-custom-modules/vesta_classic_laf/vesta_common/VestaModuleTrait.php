<?php

namespace Vesta;

use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Schema\MigrationInterface;
use Fisharebest\Webtrees\View;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Vesta\ControlPanelUtils\ControlPanelUtils;
use Vesta\ControlPanelUtils\Model\ControlPanelPreferences;
use function GuzzleHttp\json_decode;
use function redirect;
use function response;
use function route;
use function view;

trait VestaModuleTrait {

  use VestaModuleCustomTrait;
  
  protected function getVestaSymbol() {
    return json_decode('"\u26B6"');
  }

  protected abstract function getMainTitle();

  public function getTabTitle($mainTabTitle) {
    $prefix = '';
    $vesta_show = boolval($this->getPreference('VESTA_TAB', '1'));
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    return $prefix . $mainTabTitle;
  }

  public function getChartTitle($mainChartTitle) {
    $prefix = '';
    $vesta_show = boolval($this->getPreference('VESTA_CHART', '1'));
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    return $prefix . $mainChartTitle;
  }

  public function getListTitle($mainListTitle) {
    $prefix = '';
    $vesta_show = boolval($this->getPreference('VESTA_LIST', '1'));
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    return $prefix . $mainListTitle;
  }

  public function getMenuTitle($mainMenuTitle) {
    $prefix = '';
    $vesta_show = boolval($this->getPreference('VESTA_MENU', '1'));
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    return $prefix . $mainMenuTitle;
  }
  
  public function getSidebarTitle($mainTabTitle) {
    $prefix = '';
    $vesta_show = boolval($this->getPreference('VESTA_SIDEBAR', '1'));
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    return $prefix . $mainTabTitle;
  }
  
  public function title(): string {
    $prefix = '';
    $vesta_show = true/*boolval($this->getPreference('VESTA', '1'))*/;
    if ($vesta_show) {
      $prefix = $this->getVestaSymbol() . ' ';
    }
    $title = $this->getMainTitle();
    //no longer required, main title expected to be translated in Vesta Common
    /*
    if (!$this->isEnabled()) {
      $title = ModuleI18N::translate($this, $title);
    }
    */
    return $prefix . $title;
  }

  public function description(): string {
    $description = $this->getShortDescription();
    if (!$this->isEnabled()) {
      $description = ModuleI18N::translate($this, $description);
    }
    return $description;
  }
  
  public function getConfigLink(): string {
    return route('module', [
        'module' => $this->name(),
        'action' => 'Admin',
    ]);
  }

  public function getAdminAction(ServerRequestInterface $request): ResponseInterface {

    //fancy way, example from StoriesModule.php
    /*
      $this->layout = 'layouts/administration';
      return $this->viewResponse('modules/stories/config', [
      'stories'    => $stories,
      'title'      => $this->title() . ' — ' . $tree->title(),
      'tree'       => $tree,
      'tree_names' => Tree::getNameList(),
      ]);
     */

    //plain way
    return response($this->editConfig());
  }

  public function postAdminAction(ServerRequestInterface $request): ResponseInterface {
    $this->saveConfig($request);

    $url = route('module', [
        'module' => $this->name(),
        'action' => 'Admin',
    ]);

    return redirect($url);
  }

  /**
   * @return ControlPanelPreferences
   */
  protected abstract function createPrefs();

  /**
   * @return string
   */
  protected abstract function getShortDescription();
  
  /**
   * @return string[]
   */
  protected abstract function getFullDescription();

  /**
   * return html (form to edit configuration settings)
   */
  protected function editConfig() {
    ob_start();
    $this->editConfigAfterFaq();
    $editConfigAfterFaq = ob_get_clean();

    ob_start();
    $this->editConfigBeforeFaq();
    $editConfigBeforeFaq = ob_get_clean();

    ob_start();
    $utils = new ControlPanelUtils($this);
    $utils->printPrefs($this->createPrefs(), $this->name());
    $prefs = ob_get_clean();
    
    $innerHtml = view(VestaUtils::vestaViewsNamespace() . '::vesta_config', [
                'title' => $this->title(),
                'vesta' => $this->getVestaSymbol(),
                'fullDescription' => $this->getFullDescription(),
                'editConfigBeforeFaq' => $editConfigBeforeFaq,
                'editConfigAfterFaq' => $editConfigAfterFaq,
                'prefs' => $prefs
    ]);

    // Insert the view into the (main) layout
    $html = View::make('layouts/administration', [
                'title' => $this->title(),
                'content' => $innerHtml
    ]);

    return $html;
  }

  /**
   * OverrideHook
   * 
   * echoes html
   */
  protected function editConfigBeforeFaq() {
    
  }

  /**
   * OverrideHook
   * 
   * echoes html
   */
  protected function editConfigAfterFaq() {
    
  }

  /**
   * Save updated configuration settings.
   */
  protected function saveConfig(ServerRequestInterface $request) {
    $utils = new ControlPanelUtils($this);
    $utils->savePostData($request, $this->createPrefs());
  }
  
  //same as Database::getSchema, but use module settings instead of site settings (Issue #3 in personal_facts_with_hooks)
  protected function updateSchema($namespace, $schema_name, $target_version): bool {
    try {
      $current_version = intval($this->getPreference($schema_name));
    } catch (PDOException $ex) {
      // During initial installation, the site_preference table won’t exist.
      $current_version = 0;
    }

    $updates_applied = false;

    // Update the schema, one version at a time.
    while ($current_version < $target_version) {
      $class = $namespace . '\\Migration' . $current_version;
      /** @var MigrationInterface $migration */
      $migration = new $class();
      $migration->upgrade();
      $current_version++;

      //when a module is first installed, we may not be able to setPreference at this point
      ////(if this is called e.g. from SetName())
      //because of foreign key constraints: 
      //the module may not have been inserted in the 'module' table at this point!
      //cf. ModuleService.all()
      //
      //not that critical, we can just set the preference next time
      //
      //let's just check this directly (using ModuleService at this point may lead to looping, if we're indirectly called from there)
      if (DB::table('module')->where('module_name', '=', $this->name())->exists()) {
        $this->setPreference($schema_name, (string) $current_version);
      }
      $updates_applied = true;
    }

    return $updates_applied;
  }

  ////////////////////////////////////////////////////////////////////////////////
  
  public function boot(): void {    
    
    // Register a namespace for our views.
    View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    
    // and for Vesta views.
    View::registerNamespace(VestaUtils::vestaViewsNamespace(), __DIR__ . '/resources/views/');
    
    $this->onBoot();
  }

  /**
   * OverrideHook
   */
  protected function onBoot(): void {
    
  }

  ////////////////////////////////////////////////////////////////////////////////

  /**
   * OverrideHook
   */
  public function assetsViaViews(): array {
    return [];
  }

  //adapted from ModuleCustomTrait

  /**
   * Create a URL for an asset.
   *
   * @param string $asset e.g. "css/theme.css" or "img/banner.png"
   *
   * @return string
   */
  public function assetUrl(string $asset): string {
    $assetFile = $asset;
    $assetsViaViews = $this->assetsViaViews();
    if (array_key_exists($asset, $assetsViaViews)) {
      $assetFile = 'views/' . $assetsViaViews[$asset] . '.phtml';
    }

    $file = $this->resourcesFolder() . $assetFile;

    // Add the file's modification time to the URL, so we can set long expiry cache headers.
    //[RC] assume this is also ok for views (i.e. assume the rendered content isn't dynamic)
    // otherwise override assetAdditionalHash()!
    $hash = hash("md5", filemtime($file) . $this->assetAdditionalHash($asset));

    return route('module', [
        'module' => $this->name(),
        'action' => 'asset',
        'asset' => $asset,
        'hash' => $hash,
    ]);
  }
  
  //OverrideHook
  public function assetAdditionalHash(string $asset): string {
    return "";
  }

  //adapted from ModuleCustomTrait

  /**
   * Serve a CSS/JS file.
   *
   * @param ServerRequestInterface $request
   *
   * @return ResponseInterface
   */
  public function getAssetAction(ServerRequestInterface $request): ResponseInterface {
    // The file being requested.  e.g. "css/theme.css"
    $params = $request->getQueryParams();
    if (!array_key_exists('asset', $params)) {
      throw new HttpNotFoundException('No asset specified.');
    }

    $asset = $params['asset'];

    // Do not allow requests that try to access parent folders.
    if (Str::contains($asset, '..')) {
      throw new HttpAccessDeniedException($asset);
    }

    $assetsViaViews = $this->assetsViaViews();
    if (array_key_exists($asset, $assetsViaViews)) {
      $assetFile = $assetsViaViews[$asset];
      $assertRouter = function (string $asset) {
        return $this->assetUrl($asset);
      };
      $content = view($this->name() . '::' . $assetFile, [
          'assetRouter' => $assertRouter,
          'module' => $this
      ]);
    } else {
      $file = $this->resourcesFolder() . $asset;

      if (!file_exists($file)) {
        throw new HttpNotFoundException($file);
      }

      $content = file_get_contents($file);
    }

    $extension = pathinfo($asset, PATHINFO_EXTENSION);

    $mime_types = [
        'css' => 'text/css',
        'gif' => 'image/gif',
        'js' => 'application/javascript',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpg',
        'json' => 'application/json',
        'png' => 'image/png',
        'txt' => 'text/plain',
    ];

    $mime_type = $mime_types[$extension] ?? 'application/octet-stream';

    //$expiry_date = Registry::timestampFactory()->now()->addYears(10)->toDateTimeString();
    
    $headers = [
        'content-type' => $mime_type,
        //'Expires' => $expiry_date, //apparently outdated, see also EmitResponse.php, i.e. we have to use Cache-Control!
        'Cache-Control'  => 'public,max-age=31536000', //what webtrees uses elsewhere: ~10 years
    ];
    return response($content, 200, $headers);
  }
}
