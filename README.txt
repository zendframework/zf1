Welcome to the Zend Framework 1.12 Release! 

RELEASE INFORMATION
---------------
Zend Framework 1.12rc1 Release ([INSERT REV NUM HERE]).
Released on <Month> <Day>, <Year>.

NEW FEATURES
------------

* Backported autoloaders from Zend Framework 2
  * Zend_Loader_StandardAutoloader - PSR-0-compliant autoloader, with
    optimizations for specifying path/namespace or path/vendor prefix pairs.
  * Zend_Loader_ClassMapAutoloader - Use class map tables for autoloading.
  * Zend_Loader_AutoloaderFactory - Use multiple autoloader strategies.
* Backported EventManager from Zend Framework 2
  * Provides an implementation of subject/observer, publish/subscribe, signal
    slots, and traditional eventing systems.
* Zend_Cloud_Infrastructure
  * Manage IAAS services via PHP. Includes support for Amazon EC2, WindowsAzure,
    Rackspace, and GoGrid
* MVC: Create and set Cookie headers in the response
* JSON: Allow encoding objects that implement a toJson() method
* PHP 5.4 support

In all, more than 100 features and bugfixes are included in this release.

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/changelog/

MIGRATION NOTES
---------------

A detailed list of migration notes may be found at:

http://framework.zend.com/manual/en/migration.html

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.2.4 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
