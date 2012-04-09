# Server Status

This SilverStripe module exposes some server health statuses without the need to login to the server.

When this module has been installed you get three new reports in the reports admin area.

# Changelog:
1.0 - Initial release, works with SS 2.4
2.0 - Initial SS 3.0 release.

# Example of information

Below is some examples on what kind of information that are exposed.

### Server health - General

    Hostname dev.stojg.se (stig.lindqvist)
    Server software: Apache/2.2.3 (CentOS)
    PHP version:5.2.17
    Serverload: 0.02 0.06 0.07

### Server health - APC

    APC version: 3.1.6
    APC Cache full count: 77
    APC Memory used: 75% (24.1 MB / 32.0 MB)
    APC Memory fragmentation: 32.88% (2.6 MB out of 7.9 MB in 8 fragments)
    APC Cached files: 663 (24.0 MB)
    APC Shared memory: 1 Segment(s) with 32.0 MB mmap memory, pthread mutex locking

### Server health - Cache

    default Backend: Zend_Cache_Backend_File

    Cache space used: 88%
    Count of entries: 422

    primary_memcached Backend: Zend_Cache_Backend_Memcached

    Cache space used: 0%
    Count of entries: (none)

# Installation:

 - Download or clone the module into the root document folder of your SilverStripe project.
 - In the case of downloading a tar, decompress and extract.
 - Run the "dev/build".

# Links:

- https://github.com/amoebas/silverstripe-serverstatus/
- http://amoebas.github.com/silverstripe-serverstatus/