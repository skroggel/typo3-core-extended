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
    settings.resources.includeFieldsList = pages.tx_coreextended_preview_image, pages.media, tt_content.image, tt_content.assets
}
```
## Google Sitemap
This extension includes an automatically updated Google-Sitemap which can be accessed via
```
https://your-domain.com/?type=1453279478
```
## Missing Asset-Files-  - @deprecated!
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
Alternatively you can use this configuration for NGINX:
```
### Begin: Adding rewrite for missing asset files (e.g. og:image) ###
location ~* ^/(typo3temp/assets/images/((csm_.+)\.(gif|png|jpg|jpeg)))$ {
    try_files $uri $scheme://$host/index.php?type=1605802513&file=$2 =404;
}
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
## CSV-Importer
The CSV importer uses the TCA settings and can therefore be used for all tables in the context of TYPO3.
The special feature is that the CSV importer also automatically takes over the TypeCasting and also supports the import into sub-tables within an import process.
This makes it possible to import relationships between data records in different tables directly.
The CSV importer automatically recognises whether an update or insert must take place when a uid is passed in the data records.
By specifying search fields, it is also possible to search for existing data records based on defined table columns independently of specifying a uid. This prevents duplicate entries.
In addition, standard values for fields can be transferred and restrictions can be set.
### Usage
First you have to initialize the CSV-Importer and also define the primary table for the import.
```
/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = GeneralUtility::makeInstance(ObjectManager::class);

/** @var \Madj2k\CoreExtended\Transfer\CsvImporter $csvImporter */
$csvImporter = $objectManager->get(CsvImporter::class);
$csvImporter->setTableName('your_table');
```
Then you set the file or string to be read. The data can be read in either via a file or a string:
```
$csvImporter->readCsv($stringOrFile);
```
Now you have to tell the CSV-Importer which tables it is allowed to import. This is important because it can also import related sub-tables.
Make sure at least your primary table is included in the list. Otherwise nothing will be imported at all:
```
$csvImporter->setAllowedTables(['fe_users'])
```
After that you can add some additional settings described below.
Finally you do the import by calling:
```
$csvImporter->import();
```
### Feature: Import relations
Let's assume that for a table-field that creates a relation to another table, you want to import the corresponding data of the second table directly.
This can be done by providing the header of the CSV data with a prefix that corresponds to the field name in the primary table. The CSV importer then automatically resolves the relation using the TCA configuration and imports both data sets.
This is possible with unlimited nesting.

#### Example: Insert only
Example of a CSV-File for an import to `fe_users`:
```
+------------+------------+-----------------+-----------------------+--------------------------+--------------------------------+
| first_name | last_name  | usergroup.title | usergroup.description | usergroup.subgroup.title | usergroup.subgroup.description |
+------------+------------+-----------------+-----------------------+--------------------------+--------------------------------+
| Sabine     | Mustermann | Usergroup 1     | Ipsum Bibsum          | Subgroup 1               | Sub-Ipsum Bibsum               |
| Matthias   | Musterfrau | Usergroup 2     | Ipsum Lorem           | Subgroup 2               | Sub Ipsum Lorem                |
| Sam        | Person     | Usergroup 3     | Lorem Ipsum           | Subgroup 3               | Sub Lorem Ipsum                |
+------------+------------+-----------------+-----------------------+--------------------------+--------------------------------+
```
It is important that the tables concerned are also permitted for the relations AND they have to be permitted for import:
```
$csvImporter->setAllowedTables(['fe_users', 'fe_groups']); // allows both tables for import
$csvImporter->setAllowedRelationTables(
[
    'fe_users' => ['fe_groups'], // allows all realtions from fe_users to fe_groups
    'fe_groups' => ['fe_groups'] // allows all relation from fe_groups to fe_groups (used for subgroups-property)
]
);
```
The import then results in three records per row that are directly linked to each other.
1) A record in the table `fe_users` will be inserted and related via the field `usergroup` to
2) a record "Usergroup X" in the table `fe_groups`, that will be inserted and which in turn will be related via the field `subgroup` to
3) a record "Subgroup X" in the table `fe_groups`, that will be inserted and which represents the subgroup.

This works because the TCA contains the relevant information about the relations of the relevant fields and it is automatically interpreted by the CSV-Importer

fe_users:
```
return [
    'columns' => [
        'usergroup' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:fe_users.usergroup',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'enableMultiSelectFilterTextfield' => true,
                'size' => 6,
                'minitems' => 1,
                'maxitems' => 50
            ]
        ],
```

fe_groups:
```
return [
    'columns' => [
        'subgroup' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:fe_groups.subgroup',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'AND NOT(fe_groups.uid = ###THIS_UID###) ORDER BY fe_groups.title',
                'enableMultiSelectFilterTextfield' => true,
                'size' => 6,
                'autoSizeMax' => 10,
                'minitems' => 0,
                'maxitems' => 20
            ]
        ],
```

#### Example: Insert and Update
If you include e. g. the uid for the usergroup, then the CSV-Importer will work as described above, but will check if it can find the given uids and do an update instead of an insert for the corresponding record.

Example of a CSV-File for an import to `fe_users`:
```
+------------+------------+---------------+-----------------+-----------------------+--------------------------+--------------------------------+
| first_name | last_name  | usergroup.uid | usergroup.title | usergroup.description | usergroup.subgroup.title | usergroup.subgroup.description |
+------------+------------+---------------+-----------------+-----------------------+--------------------------+--------------------------------+
| Sabine     | Mustermann |             1 | Usergroup 1     | Ipsum Bibsum          | Subgroup 1               | Sub-Ipsum Bibsum               |
| Matthias   | Musterfrau |             2 | Usergroup 2     | Ipsum Lorem           | Subgroup 2               | Sub Ipsum Lorem                |
| Sam        | Person     |             3 | Usergroup 3     | Lorem Ipsum           | Subgroup 3               | Sub Lorem Ipsum                |
+------------+------------+---------------+-----------------+-----------------------+--------------------------+--------------------------------+
```
Assuming that the usergroups 1,2 and 3 exist in the database, the following will happen:

The import results in two records per row that are directly linked to each other.
1) A record in the table `fe_users` will be inserted and related via the field `usergroup` to
2) the existing record "Usergroup X" (identified via the given uid) in the table `fe_groups`, that will be updated and which in turn will be related via the field `subgroup` to
3) a record "Subgroup X" in the table `fe_groups`, that will be inserted and which represents the subgroup.

You can do that in every combination possible.

### Feature: Avoid duplicates
By specifying search fields, it is also possible to search for existing data records based on defined table columns independently of specifying an uid. This prevents duplicate entries.
```
$csvImporter->setUniqueSelectColumns(['fe_users' => ['address', 'email']]);
```
Using the setting above the CSV-importer will search the table `fe_user` using the two fields `address` and `email` and comparing it to the values of this two columns you are about to import via CSV.
If they match, the CSV-Importer will neither insert nor update the record, but set all relevant relations (if any). Existing relations are kept.

Please note: The CSV-Importer will update a record if he finds them by using the search-fields.

### Feature: Import and reference last imported row
It may be the case that you want to add two records to the same imported record.
Let's take a look at the following fictive example
```
+------------+------------+----------------------+---------------+-----------------+-----------------------+
| first_name | last_name  |        email         | usergroup.uid | usergroup.title | usergroup.description |
+------------+------------+----------------------+---------------+-----------------+-----------------------+
| Sabine     | Mustermann | mustermann@muster.de |             1 | Usergroup 1     | Ipsum Bibsum          |
| Sabine     | Mustermann | mustermann@muster.de |             2 | Usergroup 2     | Ipsum Lorem           |
+------------+------------+----------------------+---------------+-----------------+-----------------------+
```
Obviously the first two rows refer to the same person that simply belongs to two seperate usergroups.
But if you import the above example without any changes, it will result in two records for Mrs. Mustermann, each with one related usergroup
In order to achieve what we want, we can tell the CSV-Importer to refer the second row to the first by using the keyword `LAST` in an added uid-column.
```
+------+------------+------------+----------------------+---------------+-----------------+-----------------------+
| uid  | first_name | last_name  |        email         | usergroup.uid | usergroup.title | usergroup.description |
+------+------------+------------+----------------------+---------------+-----------------+-----------------------+
| 0    | Sabine     | Mustermann | mustermann@muster.de |             1 | Usergroup 1     | Ipsum Bibsum          |
| LAST | Sabine     | Mustermann | mustermann@muster.de |             2 | Usergroup 2     | Ipsum Lorem           |
+------+------------+------------+----------------------+---------------+-----------------+-----------------------+
```
The result of that import will be
1) One record in fe_users for "Sabine Mustermann" will be inserted and related via the field `usergroup` to
2) two inserted records in fe_usergroup ("Usergroup 1" and "Usergroup 2")

Please note that:
- `LAST` only works on the first level and not on sub-tables. This is because the sub-tables can only refer to one record and thus LAST makes no sense here
- `LAST` can be used several times. It always refers to the uid of the last executed insert to a table

### Setting: Exclude fields from import
If you want to exclude some fields from the import for some reasons, you can define for each importable table (and subtable) which fields will be ignored during an import:
```
$csvImporter->setExcludeColumns(
        [
            'fe_users' => [
                'hidden', 'deleted', 'tstamp', 'crdate', 'tx_extbase_type', 'TSconfig'
            ],
            'fe_groups' => [
                'hidden', 'deleted', 'tstamp', 'crdate'
            ]
        ],
    );
```

### Setting: Include fields in import
If you want to include some fields in the import for some reasons, that are not part of the TCA (e.g. `pid`), you can define for each importable table (and subtable) which fields will be added during an import.

Please note: There is no check if the fields exist in the database!
```
$csvImporter->setIncludeColumns(
        [
            'fe_users' => [
                'pid'
            ],
            'fe_groups' => [
                'pid', 'newly_included'
            ]
        ],
    );
```

### Setting: Add data explicitly to import
If you want to make sure that some fields of your CSV-import are filled with predefined values no matter what value is set via CSV, you can use the following method.
This will override the values of the CSV-data for the defined columns and also add columns that may be missing in the CSV-data.
If you want to set the values for a sub-table, you can also do this by adding the column-name as prefix

Please note: The call of `applyAdditionData()` is obligatory because this feature changes the raw imported data. This way you have to confirm the changes twice.
```
$additionalData = [
    'zip' => 'Override Value!',
    'not_included_in_csv' => 'New Column And Value!',
    'usergroup.description => 'Description override!'
];
$csvImporter->setAdditionalData($additionalData);
$csvImporter->applyAdditionalData();
```

### Setting: Set default values
If you want to set default values for some columns you can use the following method.
It will set the defined values, but the values will be overridden by the CSV-data if it contains a non-empty value (0 is interpreted as empty).
It will also add columns that may be missing in the CSV-data.

Please note: The call of `applyDefaultValues()` is obligatory because this feature changes the raw imported data. This way you have to confirm the changes twice.
```
$defaultValues = [
    'zip' => 'Default value',
    'no_included_incsv' => 'New Column and Value!',
    'usergroup.description => 'Description default value!'
];
$csvImporter->setDefaultValues($defaultValues);
$csvImporter->applyDefaultValues();
```

### Feature: TypeCasting and Sanitizing
Type-Casting and Sanitizing are done automatically based on the TCA-configuration of a column.
This includes:
- TypeCast for DateTime to timestamp based on eval
- TypeCast for Float based on eval/type
- TypeCast for Integer based on eval/type
- TypeCast based on RenderType Checkboxes (1/0)
- Fix for Links based on RenderType Links
- nl2br and wrapping P-Tag for columns with RTE-enabled
- trim for all values

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
Use `Madj2k\DrSerp\MetaTag\CanonicalGenerator->getPath()` to get the current canonical path which is generated
by ext:seo which is part of the TYPO3 Core.
This is e.g useful for generating share-links.

Example:
```
lib.txYourExtension {

    socialMedia {
        shareUrl = USER_INT
        shareUrl.userFunc = Madj2k\DrSerp\MetaTag\CanonicalGenerator->getPath
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
