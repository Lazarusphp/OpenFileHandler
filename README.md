# OpenFileHandler

*Version Information*
Build version : v1.0.0
Date Of Build : 

## What is openFileHandler
Open File Handler (OFH) is a standalone File and Directory Handling Library, OFH is designed to control and cominicate with the Server file structure, set permissions and manage files and directories.

## The FileWriter Library.
As Part of the newly designed Open File Handler library it will now incorperate the use of the lazarusphp FileWriter Library giving more control relating to files and folders.

## Open File handler Permissions manager
Along side the FileWriter and File handler the Library will also feature a permissions manager. The Permissions manager will give the FileHandler library the ability to set permissions relating to the files and directories.

## Open Filehandler Structure

* Src
    * Traits
        Permissions.php
        Structure.php
    * CoreFiles
        * FileWriterCore.php
        * FileHandlerCore.php
    * Interfaces
        * FileWriterInterface.php
        * FileHandlerInterface.php
        * PermissionsInterface.php
        * StructureInterface.php
    * FileWriter.php
    * FileHandler.php


## Usage

*Installation*

```
composer install lazarusphp/openfilehandler
```

```php
// Make Sure path exists
FileHandler::createDirectory("/Apps/Login");
```

*Listing Files and Folders*

```php
FileHandler::listAll("/Apps");
```

*Deleting Directory and files*

```php
FileHandler::deleteDirectory("/Apps");
```