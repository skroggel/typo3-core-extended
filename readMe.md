# core_extended
# core_extended

## Custom Error Message For Content Rendering <a name="error-messages"></a>
Using the following configuration you can return a custom message upon an error in the frontend.
```
config {
``
	[...]

    //===============================================================
    // Exceptions for FE-Rendering
    //===============================================================
    // Custom class
    contentObjectExceptionHandler = Madj2k\CoreExtended\Error\ContentObjectProductionExceptionHandler

    // Customizing error message
    contentObjectExceptionHandler.errorMessage = Leider ist ein Fehler aufgetreten. Helfen Sie uns, den Fehler zu beheben und schreiben Sie uns unter Angabe des Fehlercodes "%s" an <a href="mailto:service@example.de?subject=Errorcode:%20%s">service@example.de</a>

    // Ignore these error codes
    // contentObjectExceptionHandler.ignoreCodes.10 = 1414512813

```
## Copyright Information for Media
Use the plugin to output the associated copyright information for all your used media. This is especially helpful for stock images.
You can also use it via Typoscript e.g. to print the copyright information for media-sources on each page:
```
imageResources = USER
imageResources {
    userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
    extensionName = CoreExtended
    pluginName = MediaSources
    vendorName = Madj2k
    controller = MediaSources
    switchableControllerActions {
        // Again: Controller-Name and Action
        MediaSources {
            1 = listPage
        }
    }

    view =< plugin.tx_coreextended.view
    persistence =< plugin.tx_coreextended.persistence
    settings =< plugin.tx_coreextended.settings
    settings.includeFieldsList = pages.tx_coreextended_preview_image, pages.media, tt_content.image, tt_content.assets
}


TxCoreExtendedAssetNotFound = PAGE
TxCoreExtendedAssetNotFound {
    typeNum = 1605802513
    config {
        disableAllHeaderCode = 1
        xhtml_cleaning = 0
        admPanel = 0
        no_cache = 0
        debug = 0

        metaCharset = utf-8

        index_enable = 0
        index_metatags = 0
        index_externals = 0
    }

    10 = USER_INT
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        extensionName = CoreExtended
        pluginName = AssetNotFound
        vendorName = Madj2k
        controller = NotFound
        switchableControllerActions {
            # Again: Controller-Name and Action
            NotFound {
                1 = assets
            }
        }

        view < plugin.tx_coreextended.view
        persistence < plugin.tx_coreextended.persistence
        settings < plugin.tx_coreextended.settings
    }
}
```
## Google Sitemap
This extension includes an automatically updated Google-Sitemap which can be accessed via
```
https://your-domain.com/?type=1453279478
```
## Missing Asset-Files- Handler
When using rendered images, it may be the case that a clearing of all caches
results in 404-errors for images that have been shared via SocialMedia - but now have a different hash-value appended.
The handler tries to find the image by searching for a file that begins with the same filename and setting a symlink to it.

Implement this directive in your Apache-configuration to activate the handler:
```
### Begin: Adding rewrite for missing asset files (e.g. og:image) ###
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(typo3temp/assets/images/((csm_.+)\.(gif|png|jpg|jpeg)))$ https://%{HTTP_HOST}/index.php?type=1605802513&file=$2 [R=301,NC,L]
</IfModule>
### End: Adding rewrite for missing asset files (e.g. og:image) ###
```
## StoragePidAwareAbstractRepository
This extension comes with an abstract repository class, which fixes the troublesome behavior
of repositories to use the storagePid of the calling extension.

If your repository extends this class, it will always use its own storagePid - even if called through another extension

## Slug-Helper for Routing with Aspects
This slug-helper normalized the generated routes when loading table-fields into the routes.
This is especially useful when working with German Umlauts.
```
routeEnhancers:
  RkwAuthors:
    type: Extbase
    namespace: 'tx_rkwauthors_rkwauthorsdetail'
    routes:
      - routePath: '/rkw-author/{author}'
        _controller: 'Authors::show'
      - routePath: '/rkw-authorform/{author}'
        _controller: 'Authors::contactFormSend'
    defaultController: 'Authors::show'
    aspects:
      author:
        type: PersistedSlugifiedPatternMapper
        tableName: 'tx_rkwauthors_domain_model_authors'
        routeFieldPattern: '^(.*)-(?P<uid>\d+)$'
        routeFieldResult: '{first_name|sanitized}-{last_name|sanitized}-{uid}'
```

## Simulate Frontend in Backend Context
This is extremely useful when working with CLI-commands or UnitTests and you need TYPO3 to behave like in frontend-context.
If using it in the context of UnitTests, be aware that you MUST define a domain AND a page-object!
Example:
### Rootpage.typoscript
```
page = PAGE
page {
    10 = TEXT
    10.value = Hallo Welt!
}
```

### Global.xml
```
dataset>
    <pages>
        <uid>1</uid>
        <pid>0</pid>
        <title>Rootpage</title>
        <doktype>1</doktype>
        <perms_everybody>15</perms_everybody>
    </pages>
</dataset>
```

### config.yaml
```
base: www.example.com
languages:
  -
    title: Deutsch
    enabled: true
    base: /
    typo3Language: de
    locale: de_DE.UTF-8
    iso-639-1: de
    navigationTitle: ''
    hreflang: de-DE
    direction: ltr
    flag: de
    languageId: '0'
  -
    title: Englisch
    enabled: false
    base: /en/
    typo3Language: default
    locale: en_US.UTF-8
    iso-639-1: en
    navigationTitle: ''
    hreflang: ''
    direction: ''
    flag: gb
    languageId: '1'
    fallbackType: strict
    fallbacks: ''
rootPageId: '{rootPageId}'
routes: {  }
imports:
  - { resource: "EXT:core_extended/Configuration/Routes/Default.yaml" }

routeEnhancers:
  #========================================
  # PageTypes
  #========================================
  PageTypeSuffix:
    type: PageType
    default: '/'
    index: ''
    map:

      # defaults and trailing slash
      '/': 0
      'print/': 98
      'xml/': 150
      'content-only/': 160
      'plaintext/': 170
      'csv/': 180
```
### Your setUp() in your test-file
```
    $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');
    $this->setUpFrontendRootPage(
        1,
        [
            'EXT:core_extended/Configuration/TypoScript/setup.txt',
            self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
        ],
        ['example.com' => self::FIXTURE_PATH .  '/Frontend/Configuration/config.yaml']
    );

    FrontendSimulatorUtility::simulateFrontendEnvironment(1);
```
### Your tearDown() in your test-file
```
    FrontendSimulatorUtility::resetFrontendEnvironment();
```
## Meta-Tag-Generator: Robots
The extension comes with a Meta-Tag-Generator for the noindex- and nofollow-attributes of ext:seo.
The main difference is, that both attributes are inherited to the corresponding subpages.
This way you are able to set noindex and/or nofollow to a whole page-tree. This is useful
when you are about to set up a new website and don't want it to be crawled, yet.

## Meta-Tag-Generator: Meta-Tags
The extension comes with a Meta-Tag-Generator for the keywords- and description-attributes.
The main difference is, that both attributes are inherited to the corresponding subpages.
This way you are able to set keywords and/or description to a whole page-tree.

## Meta-Tag-Generator: Canonical-Path
Use `Madj2k\CoreExtended\MetaTag\CanonicalGenerator->getPath()` to get the current canonical path which is generated
by ext:seo which is part of the TYPO3 Core.
This is e.g useful for generating share-links.

Example:
```
lib.txYourExtension {

    socialMedia {
        shareUrl = USER_INT
        shareUrl.userFunc = Madj2k\CoreExtended\MetaTag\CanonicalGenerator->getPath
        shareUrl.stdWrap.rawUrlEncode = 1
    }
}
```
```
<a href="https://twitter.com/intent/tweet?url={f:cObject(typoscriptObjectPath:'lib.txYourExtension.socialMedia.shareUrl')}">
    Share on Twitter
</a>
```
## TypoScript Libs
This extension includes Libs for
* adding a combined page-title to your page-header
* adding openGraph-data to your page header, including a watermark and copyright information

Example for usage:
```
page {
    headerData {

        // Title-Tag
        1010 < lib.txCoreExtended.titleTag

        // OpenGraph
        1030 < lib.txCoreExtended.openGraph
    }
}
```
## TypoScript-Conditions
The extension comes with three TypoScript-Conditions:
### BackendColPos (Backend-Context)
This condition is helpful e.g. in order to allow or disallow content elements for backend editors depending on the colPos they are editing.

Usage:
```
[backendColPos() == 111]
    // do whatever
[END]
```
### BackendLayout (Backend-Context)
This condition is helpful e.g. in order to allow or disallow content elements for backend editors depending on the backend layout. It also checks for the backend_layout_subpages field.

Usage:
```
[backendLayout() == 'pagets__homePages']
    // do whatever
[END]
```
### ExtensionLoaded (Backend- and Frontend-Context)
This condition checks if a specified extension is installed

Usage:
```
[extensionLoaded('fictive_ext')]
    // do whatever
[END]
[! extensionLoaded('fictive_ext')]
    // do whatever
[END]

```

## Standard-Partials for FlashMessages and FormErrors
This extension comes with two standard files for FlashMessages and FormErrors for usage in your own extensions.

Usage:
1. Add partials to your own extension
```
plugin.tx_yourextension {

    view {
        partialRootPaths {
            0 = EXT:yourextension/Resources/Private/Partials/
            1 = {$plugin.tx_yourextension.view.partialRootPath}
            2 = {$plugin.tx_coreextended.view.partialRootPath}
        }
    }
}
```
2. Refer to partials as usual
```
<f:render partial="FlashMessages" arguments="{_all}" />
<f:render partial="FormErrors" arguments="{for:yourObject}"/>
```

## Additional features for usage of sr_freecap
This extension fixes some bugs in sr_freecap (if the extension is installed).
Beyond that it
- adds a basic partial which prevents you from copying the same properties and partials in each of your extensions and makes it possible to use sr_freecap optional in your own extenions
- adds a custom validator which makes it possible to use sr_freecap optional in your own extenions

Usage:
1. Add partials to your own extension
```
plugin.tx_yourextension {

    view {
        partialRootPaths {
            0 = EXT:yourextension/Resources/Private/Partials/
            1 = {$plugin.tx_yourextension.view.partialRootPath}
            2 = {$plugin.tx_coreextended.view.partialRootPath}
        }
    }
}
```
2. Extend the AbstractCaptcha-class in your model. This will add the necessary fields for sr_freecap to work. There is no need to extend your database tables!
```
<?php
namespace YourNamespace\YourExtension\Domain\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Madj2k\CoreExtended\Domain\Model\AbstractCaptcha;

/**
 * Class MyClass
 *
 * @author You <you@you.de>
 * @copyright You
 * @package YourNamespace_YourExtension
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MyClass extends AbstractCaptcha
{

```
3. Add the validator to the desired action of your controller to validate the captcha
```
    /**
     * action create
     *
     * @param \YourNamespace\YourExtension\Domain\Model\MyClass $class
     * @return void
     * @TYPO3\CMS\Extbase\Annotation\Validate("Madj2k\CoreExtended\Validation\CaptchaValidator", param="class")
     */
    public function createAction(
        \YourNamespace\YourExtension\Domain\Model\MyClass $class,
    ): void {
```
4. Add Captcha to your form
```
<f:render partial="CaptchaElement" />
```

## XClasses
* Adds additional cookie "fe_login" which is accessible via JavaScript if a user is logged in. This can be used e.g. to trigger AJAX-calls which are relevant for logged in users only.
* Extends \TYPO3\CMS\Extbase\Service\EnvironmentService::isEnvironmentInFrontendMode() and \TYPO3\CMS\Extbase\Service\EnvironmentService::isEnvironmentInBackendMode() so that these methods also check for $GLOBALS['TSFE'].
* Fixes wierd bug in \TYPO3\CMS\Extbase\Service\ExtensionService which throws an exception for no reason

## Some generic methods, Validators and ViewHelpers
[to be described later]
