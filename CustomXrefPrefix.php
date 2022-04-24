<?php

/**
 * Custom XREF Prefix module.
 */

declare(strict_types=1);

namespace CustomXrefPrefix;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Registry;

use Cissee\Webtrees\Module\ClassicLAF\Factories;

require_once __DIR__ . '/vendor/vesta-webtrees-2-custom-modules/vesta_classic_laf/vesta_classic_look_and_feel/Factories/CustomXrefFactory.php';

/**
 * Class CustomXrefPrefix
 *
 *
 * Modules *must* implement ModuleCustomInterface.  They *may* also implement other interfaces.
 */
class CustomXrefPrefix extends AbstractModule implements ModuleCustomInterface
{
    // For every module interface that is implemented, the corresponding trait *should* also use be used.
    use ModuleCustomTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Custom XREF Prefix');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Provides custom XREF prefixes rather than always using "X"');
    }

    /**
     * The person or organisation who created this module.
     *
     * @return string
     */
    public function customModuleAuthorName(): string
    {
        return 'Matt N.';
    }

    /**
     * The version of this module.
     *
     * @return string
     */
    public function customModuleVersion(): string
    {
        return '0.1.0';
    }

    /**
     * A URL that will provide the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersionUrl(): string
    {
        return 'https://github.com/mnoorenberghe/webtrees-custom-xref-prefix/raw/main/latest-version.txt';
    }

    /**
     * Where to get support for this module.  Perhaps a github repository?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return 'https://github.com/mnoorenberghe/webtrees-custom-xref-prefix/issues/';
    }

    /**
     * Called for all *enabled* modules.
     */
    public function boot(): void
    {
        Registry::xrefFactory(new Factories\CustomXrefFactory($this));
        // This factory already defaults to using the first letter of the record type otherwise.
        $this->setPreference('MEDIA_ID_PREFIX', 'M');
    }
}
