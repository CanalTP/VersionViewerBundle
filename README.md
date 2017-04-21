VersionViewerBundle
===================

This Bundle provide a simple interface to load information about application deployed in multi instance environment contexte.

 
## Installation Using Composer

To install this bundle with Composer just add the following line to your composer.json file.


    // composer.json
    {
        // ...
        "require": {
            // ...
            "bbr/version-viewer-bundle" : "dev-master"
        }
    }

 
Please replace dev-master in the snippet above with the latest stable branch, for example `1.0.*`.
 
Then, you can install the new dependencies by running Composer's `update` command from the directory where your composer.json file is located:


    php composer.phar update
 
Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your `AppKernel.php` file, and register the new bundle: 
 

    // in AppKernel::registerBundles()
    public function registerBundles()
    {
        $bundles = array(
            //...
            new Bbr\VersionViewerBundle\bbrVersionViewerBundle(),
        );


Then configure the bundle with the required parameters in config.yml (see [the Configuration section](#config))

Then, you to have import routes in `routing.yml` and add optionally a prefix :


    bbr_version_viewer:
        resource: "@bbrVersionViewerBundle/Resources/config/routing.yml"
        prefix:   /


Finally you need to install assets


    php bin/console assets:install --symlink

 
## <a name="config"></a>Configuration

configuration contains four main sections :

### Application configuration 

This section contains general settings about the application itself.


 
    bbr_version_viewer.appConfig:
        feedback_email: #section for feedback form
          enabled: true
          to: receiver@mail.com
          from: versionviewer@canaltp.fr
        analytics:
          enabled: true
          uid : ABCDEF-123

#### Feedback form

The `feedback_email` section is optionnal. If `enabled` is  set to `true` you must specify `from ` and `to` parameters. By default `enabled` is set to `false.`

#### Analytics
You can specify an anlytics tag to gather stats. If  `enabled` is  set to `true` you must specify `uid `  parameter. By default `enabled` is set to `false.`

### Environment configuration

This section define all available environment accross all existing application.


    bbr_version_viewer.environments:
        dev:
            # environment name
            name: development      # Example: development
            trigram: dev           # Example: dev
        prod:
            name: production
            trigram: prod


Elements declared in this section are used in [the URLHandler Configuration section](#urlHandlerConfig)

### <a name="urlHandlerConfig"></a>URLHandler Configuration 


This section define the handler who handle the host name used by an application in the environment.  

In this example `MyURLHandler` is a `TemplatedURLHandler` (see below for what is a TemplatedURLHandler). 


    bbr_version_viewer.urlHandler:
        MyURLHandler:
              type: TemplatedHostURLHandler
              envHosts:
                dev: dev
                int: internal
                prod: prod
              envHostSuffix: .domain.com

This configuraiton will generate host for dev, internal and production environment :
  * .dev.domain.com
  * .internal.domain.com
  * .production.domain.com


Elements declared in this section are used in [the ApplicationType Configuration section](#applicationTypeConfig) 

#### Type of URLHandler

##### TemplatedHostURLHandler 

This type is adapted for application with normalized host accross environment. 

Example :
  * appname.dev.domain.com
  * appname.internal.domain.com
  * appname.production.domain.com


### <a name="applicationTypeConfig"></a>Application Type Configuration

This section describe Application Type. 
An application type describe :
  * the path where to collect information (ie : URL where to gather information)
  * the type of loader. You can load over HTTP or via NRPE client. 
  * the nature of the source (ie : XML, text, ...)
  * the information to retrieve. The format of the configuration for this item may be different according to the defined type. 
  * and the comparison value

This example describe four differents applications types  :


    bbr_version_viewer.applications_type:
        myfirstapptype:
            id: mfat
            name: My First App Type
            releaseFilePath: /file.txt
            fileType: text
            filteredProperties: 
              version: Release tag :(.*)
              release date: Release date :(.*)
            comparisonValue: version
        mysecondapptype:
            id: msat
            name: My Second App Type
            releaseFilePath: /my/path/to/file.xml
            fileType: xml
            filteredProperties:
              start date: .//first/infos
              end date: .//ProductionDate/EndDate 
              version: //Version
            comparisonValue: version
        mythirdapptype:
            id: mtat
            name: My Third App Type
            releaseFilePath: /file.json
            fileType: json
            filteredProperties: 
              version: tag
              release date: released_at
              deploy date: deployed_at
            comparisonValue: version
        myfourthapptype:
            id: mfat
            name: My Fourth App Type
            releaseFilePath: my_nrpe_command
            releaseFileLoader: nrpe
            fileType: json
            filteredProperties: 
              version: tag
              release date: released_at
              deploy date: deployed_at
            comparisonValue: version


Elements declared in this section are used in [the Applications Configuration section](#applicationConfig) 

#### <a name="releaseFileLoader"></a>Release File Loader Type

The parameter `releaseFileLoader` define which type of loading method will be used to retrieve information. If this configuration is ommited a loading over HTTP will be used. 

If you specify `releaseFileLoader: nrpe` the NRPE client will be used to retrieve data. See [the NRPE annexe section](#NRPEAnnexe) for system requirement.

__Note __: Actually the NRPE naive implementation launch this command ` /usr/lib/nagios/plugins/check_nrpe -H my.host.name -c get_json -a /path/to/my/file.ext` the parameter `-c get_json`is dependent to your NRPE script implementation.


#### Release File Path propertie

The `releaseFilePath`propertie is used for both NRPE and HTTP [Release File Loader Type](#releaseFileLoader)

* With an HTTP loader type you must specify the path to the release file. (ie : `/path/to/my/file.ext`)
* With an NRPE loader type you must specify the path to the release file. (ie : `/path/to/my/file.ext`) This value is passed to the `-a` parameter (ie : `... -c get_json -a /path/to/my/file.ext ...`) 

#### File Type and Filtered Properties

There are three type of field supported with some limitation for some of them.  

For the `FilteredProperties` content, keys are value which will be displayed in UI, value the "path" to value in requested file. 

Note : You can override this properties at the application configuration level. See [ReleaseFileOverwrite section for more details](#releaseFileOvewrite)

##### Text type

This type is for Text file, basically it use regexp, so it can be used against all type of ressource.  
Filtered properties must be defined as a pattern to match (see `My First App Type` above for example and use service like http://regexr.com/ to test your regexp).

##### Xml type
This type is for XML ressource. Bascially it use Xpath to request attribute.
Filtered properties must be specified as Xpath request. (see `My Second App Type` above for example ).

##### Json type 

This type is for json file, basically it proceed json with `json_decode` PHP function.  
Filtered properties must be defined in javascript notation. (see `My Third App Type` above for example ).

/!\ Warning : for the moment you can only request an item of one level depth in this type of file !

### <a name="applicationConfig"></a>Application Configuration

This section define configuration for each application.

Example of a full configuration of an application:


    bbr_version_viewer.applications:
        myapplication:
            URLHandler:
              default:
                handler: MyURLHandler #define four environment (dev, internal, customer, prod) with a default domain suffix in mydomain.com
                appHost: myapp
              prod: 
                handler: FullHostURLHandler
                appHost: myapp.whatever.anotherdomain.com
            ReleaseFileLoader:
              prod:
                https: true
                timeout: 5

##### URL Handler Configuration

###### Environment configuration

This sub section define the specific part of the application instance.
With `default` key you can define the default sub domain name used for this application.
You can also define a specific configuration for an environment by define it by its key.
You can define if an environnement is in https. If not specified http will be used. 

###### <a name="releaseFileOvewrite"></a>ReleaseFileOverwrite

In this section you can override some parameters defined by the [application Type](#applicationTypeConfig).

You can override parameters for a specific environment by specifying it by its key or override for all environment by using `default`key.
Configuration are interpreted in this order :

* application type
* application `default` override
* application environment specific override

    bbr_version_viewer.applications:
        myapplication:
            URLHandler:
              default:
                handler: MyURLHandler
                appHost: myapp
            ReleaseFileOverwrite:
                prod:
                    releaseFilePath: /different/path
                default:
                    filteredProperties: 
                        version: mypath/to/version
                        release_date: ~
            ReleaseFileLoader:
              cus:
                https: true
                timeout: 5        

You can overwrite default configuration of the file path by environment by define it by its key.

You can override the following parameters :

* releaseFilePath
* filteredProperties
    
__Note 1__ : Filtered Properties is an array and a merged is done with aplication type configuration.

__Note 2__ : you can ignore a filtered properties by using the ~ char as in `release_date` example above.
  

##### ReleaseFileLoader Configuration

In this section you can define specific configuration for the ReealseFile Loader

##### HTTPReleaseFileLoader Configuration

You can override parameters for a specific environment by specifying it by its key or override for all environment by using `default`key.

Available option for this loader :
    * `https: true | false` : define if URL is accessible over https or not. by default set to false.
    * `timeout: integer` : define the timeout for the instance. 

__Note__ : timeout is not configurable at the application Type level but only at the instance level. Default value is 2 seconds. 

##### NRPEReleaseFileLoader Configuration

None option at the moment.

## Annexes

### <a name="NRPEAnnexe"></a>NRPE Client

To load release file using [NRPE client](#releaseFileLoader) you have to install NRPE client package.

Example on Ubuntu :

    sudo apt-get install nagios-nrpe-plugin
    

AND you need to have PHP `exec` method enabled !
