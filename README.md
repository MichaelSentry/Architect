NinjaSentry Architect
=====================

#Sync 
> Recursively clone the contents of a local directory to another local directory

A weekend idea for building a simple app-skeleton generator.  
 
Inspired by the Unix Philosophy :  

> "Rule of Generation: Avoid hand-hacking; write programs to write programs when you can."

## Setup a Sync config file

```php 

$config = [  
  
    // 'basePath'    => 'var/www/html/'
    'basePath'    => 'C:/UwAmp/www/',
    
    // all dirs/files are relative to the base path 
    'projectName' => 'test-skeleton', // output directory
    'dirMap'      => 'architect/project/test-skeleton/dirmap.php',
    'fileMap'     => 'architect/project/test-skeleton/filemap.php',
    'sources'     => 'app-skeleton/public_html/' // input directory
];

```

### Directory Map

Contains the list of directories to be created

Windows example:  
C:/UwAmp/www/architect/project/test-skeleton/dirmap.php

Linux example :  
var/ww/html/architect/project/test-skeleton/dirmap.php

```php 

return [
    'app/components/home/',
    'app/config/mode/development/',
    'app/config/mode/production/',   
    'app/config/mode/staging/',
    'app/config/firewall/policy/',
    'app/content/blog/published/',
    'app/content/error/',
    'app/content/layouts/',
    'app/content/modules/',
    'app/content/partials/',
    'app/tmp/audit/',
    'app/tmp/audit/login/failed/',
    'app/tmp/audit/login/last/',
    'app/tmp/audit/login/locked/',
    'app/tmp/cache/',
    'app/tmp/firewall/',
    'app/tmp/logs/',
    'app/tmp/session/',
    'public/assets/',
    'public/themes/offline/',
];

```
### Files Map

Contains the list of files to be cloned

Windows example :   
C:/UwAmp/www/architect/project/test-skeleton/filemap.php

Linux example :   
var/ww/html/architect/project/test-skeleton/filemap.php

```php 

return [
    /**
     * Copy Htaccess files
     */
    '.htaccess',
    'public/.htaccess', 
    
    /**
     * Copy Public index
     */
    'public/dispatch.php',
    'public/robots.txt',
    'public/favicon.ico',  
      
    /**
     * Copy Bootstrap
     */
    'app/bootstrap.php',
    
    // etc etc
];

```

### Clone all directories and files

```php 

$sync = (new Sync( $config ))->clone();

```
    
### Get Sync report

```php 

$report = $sync->report();

```

Todo :  

Add web interface / form  
 - Project file generator
 - read/write project config files

Remote Sync  
server to server cloning ( transloader )
  
more later..
