# core_extended
## 1. Features
### 1.1 Custom Error Message For Content Rendering
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
### 1.2 Frontend-Cache Interface
There extension contains`` an interface for the frontend caching in TYPO3. You can use it by extending ``Madj2k\CoreExtended\Cache\CacheAbstract``

### 1.3 Media Sources
### 1.4 Google Sitemap
### 1.5 Missing Asset-Files- Handler
### 1.6 Slug-Helper for Routing
### 1.7 Simulate Frontend in Backend Context
### 1.8 Some generic methods and ViewHelpers
