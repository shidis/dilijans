JSmart Compressor v1.0 Final Release
Copyright (c) 2006 by Ali Farhadi.
Released under the terms of the GNU Public License.
See the GPL for details.

Email: ali@farhadi.ir
Website: http://farhadi.ir/



=== What is JSmart? ===
JSmart is an open source PHP program that speeds up your website by reducing the size of JavaScript and CSS files typically up to 80%.
It is done by removing comments and unnecessary whitespaces and then by compressing the file using gzip.



=== JSmart Features ===
- JSmart will increase your website download speed by reducing the size of JavaScript and CSS codes typically up to 80% or even higher.
- If your website have a large amount of JavaScript and CSS codes, JSmart will reduce your monthly bandwidth usage amazingly.
- JSmart uses a smart cache system in both the client side and the server side.
- It is smart about recompressing only the files that have changed.
- It is also smart about gzipping the files if the browser supports gzip encoding.
- It also makes the browser to cache the files until they have not changed.
- There is no need to do any thing manually. JSmart will compress all of JavaScript and CSS files just after installing it on your website.
- JSmart will automatically compress/recompress the files when you add/change them.
- By using Apache mod_rewrite, there is no need to change even one line of code at your project. You should only copy JSmart to your website and enjoy it.



=== Intallation Requirements ===
- PHP 4.3.0 or higher.
- Apache is recommended.



=== Installation Intructions ===
- Unzip jsmart.zip and copy jsmart folder to your website.
- JSmart will need write access to the cache folder (placed in jsmart folder).
- if Apache mod_rewrite is enabled on your website, copy .htaccess file to your website if it is not already exist. otherwise, if it is already exist, copy the content of .htaccess file to the end of your current .htaccess file.
- if Apache mod_rewrite is not enabled or if your web server is not Apache, you should change all of JavaScript and CSS urls used on your website from "path/to/file/filename" to "jsmart/load.php?file=path/to/file/filename".
  See the following examples : 
	Script tag :
		<script src="jsmart/load.php?file=path/to/file/jsfile.js"></script>
	CSS link :
		<link rel="stylesheet" href="jsmart/load.php?file=path/to/file/cssfile.css" />
	CSS @import rule :
		@import url("<b>jsmart/load.php?file=path/to/file/cssfile.css</b>");
	


=== Advanced Configurations ===
JSmart has a configuration file named config.php.
The following configurations can be defined at this file. However it will work with default configurations.

  - JSMART_DEBUG_ENABLED: true or false.
    By setting this option to true, a message containing error details will be alerted if any error occurs.
    So it will be very helpful setting this option to true if JSmart doesn't work for you (that it may be due to a misconfiguration or bad installation).
  - JSMART_CHARSET: utf-8 or iso-8859-1
    The charset of your js and css files.
  - JSMART_JS_DIR: a relative or an absolute path
    This is the base directory for js files (path of the file that is passed to load.php are relative to this path).
  - JSMART_CSS_DIR: a relative or an absolute path
    This is the base directory for css files (path of the file that is passed to load.php are relative to this path).
  - JSMART_CACHE_ENABLED: true or false
    You can disable JSmart cache by setting this option to false (useful for development and debugging purposes).
  - JSMART_CACHE_DIR: a relative or an absolute path
    This is JSmart cache directory for saving compressed js and css files.	
	

      
=== Version History ===

v1.0 Final Release - July 7 2006
  - Ability of compressing CSS files was added. (thanks to Alexey Kuimov)
  - Content-Type of js files was corrected. (from text/plain to application/x-javascript)


v1.0 RC2 - July 1 2006
  - The charset of js files can be specified through config file.
  - Debugging capabilities was added.
  - Debug mode can be enabled or disabled using config file.
  - Cache system can be disabled using config file (useful for development and debugging purposes).
		
		
v1.0 RC - June 29 2006
  - JSmart is more customizable by using config.php
  - cache folder can be specified through config file.
  - JavaScript base dir can be specified through config file.
  - JSmart will not crash if zlib extension is not installed.

		
v1.0 beta - June 25 2006
  - It is the first release