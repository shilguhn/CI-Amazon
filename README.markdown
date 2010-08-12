CodeIgniter-Amazon
===================

CodeIgniter-Amazon is a CodeIgniter library which assists in the 
development of applications using the Amazon API. 

It's focused on retrieving books with a keyword search, but can be modified to search other areas as well.

Note, this is a very simple implementation and requires some more love, but SHOULD work out of the box.

Usage
-----

	$this->load->library('Amazon_API');
	$this->amazon_api->getBooks("guide, travel, rome");

Future
------

Add error-handling and clean up code.
	              
	
Thanks
------

Based on http://www.kennylucius.com/a/AAWS_class_definition