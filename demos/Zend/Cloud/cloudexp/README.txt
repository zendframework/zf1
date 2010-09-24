Cloud Explorer
--------------

Cloud Explorer is written as a demonstration of the Simple Cloud API as
implemented in Zend Framework (Zend_Cloud component). It provides the
ability:

 * to browse collections within a document storage, and to add and
   delete documents from collections
 * to create queues, and to send and receive messages from queues
 * to upload and retrieve files to and from a storage service

To try it out:

 * You will either need Zend Framework on your include_path, or you will
   need to symlink it into the library/ subdirectory.
 * You will need to create a virtual host pointing at the public/
   subdirectory as the DocumentRoot.
 * You will need to copy application/configs/application.ini.dist to
   application/configs/application.ini, and edit it to point at the
   appropriate services, and to provide the appropriate credentials for
   those services.

Once you have accomplished the above, simply fire up a browser and point
it to your virtual host.
