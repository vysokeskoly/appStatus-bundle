App Status Bundle
=================

[![Latest Stable Version](https://img.shields.io/packagist/v/vysokeskoly/app-status-bundle.svg)](https://packagist.org/packages/vysokeskoly/app-status-bundle)
[![License](https://img.shields.io/packagist/l/vysokeskoly/app-status-bundle.svg)](https://packagist.org/packages/vysokeskoly/app-status-bundle)
[![Checks](https://github.com/vysokeskoly/appStatus-bundle/actions/workflows/checks.yaml/badge.svg)](https://github.com/vysokeskoly/appStatus-bundle/actions/workflows/checks.yaml)
[![Build](https://github.com/vysokeskoly/appStatus-bundle/actions/workflows/php-checks.yaml/badge.svg)](https://github.com/vysokeskoly/appStatus-bundle/actions/workflows/php-checks.yaml)
[![Coverage Status](https://coveralls.io/repos/github/vysokeskoly/appStatus-bundle/badge.svg)](https://coveralls.io/github/vysokeskoly/appStatus-bundle)

This bundle adds a `Collector` which adds buildinfo into your Symfony profiler.

## Changelog
See CHANGELOG.md.

## Install

### Step 1

Download using *composer*

    "require": {
        "vysokeskoly/app-status-bundle" : "^5.0"
    },

### Step 2

Add `AppStatusBundle` to AppKernel to list of loaded bundles (under `dev` bundles).  

**AppKernel.php**

```php
public function registerBundles()
{
    $bundles = [
        ...
    ];
    
    if (in_array($this->getEnvironment(), array('dev'))) {
        ...
        $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        $bundles[] = new VysokeSkoly\AppStatusBundle\VysokeSkolyAppStatusBundle();
        ...
    }
}
```

### Step 3

Configure required parameters for this bundle.

**config.yml**

```yaml
    # AppStatus Bundle
    vysoke_skoly_app_status:
        source_file: "PATH_TO_BUILDINFO.XML"
        main_status_key: "MAIN_STATUS_KEY"
```

#### source_file
You can specify `source_file` which is XML file with package info.
Default value is `var/buildinfo.xml`.

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<appStatus>
    <name>app-status</name>
    <version>2017.03.08.16.30.28-68.gad10e8af8</version>
    <sourceRevision>ad10e8af8814f825e36e629ab1a19c5078a6d257</sourceRevision>
    <repository>
        ssh://git/app-status-bundle.git
    </repository>
    <buildNumber>666</buildNumber>
    <buildBranch>feature/app-status-bundle</buildBranch>
    <buildUrl>https://jenkins/job/app-status/666/</buildUrl>
    <project>app_status_bundle</project>
    <hostName>__HOSTNAME__</hostName>
</appStatus>
```

#### main_status_key
This is an optional property which tells the collector that you want to see one of status values right in the profiler.
Default value is `buildBranch`
