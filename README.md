<h1>Pixelant</h1>

[![TYPO3](https://img.shields.io/badge/TYPO3-8.3.0-orange.svg?style=flat-square)](https://typo3.org/)

<h2>Pixelant Dashboard (dashboard)</h2>
This is an extension, that is an dashboard. Which means that you can include showing stats from Google Analytics or News from Typo3 CMS. Sure, it's easy extendable.

[Documentation](#documentation) &nbsp; [How to configure it?](#configure) &nbsp; 

<a name="documentation"/>
## Documentation

For all kind of documentation which covers install to how to develop the extension:

https://pixelant.gitbooks.io/

<a name="configure"/>
## How to configure it?

Use extension manager to install extension

Include TypoScript template. Include static (from extensions): "Dashboard (dashboard)".

> NOTE: Check Storage Pid
> If you use T3kit Blue Mountain installation or your website root page uid != 1
> Go to Template -> Constant Editor -> Change CATEGORY on MODULE.TX_DASHBOARD (4)
> Then go to Others and change Default storage PID [module.tx_dashboard.persistence.storagePid] on yours uid.
