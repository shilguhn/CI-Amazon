<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Amazon API
 * 
 * A Amazon API Library for CodeIgniter based off Amazons own examples (http://developer.amazonwebservices.com/connect/entry.jspa?externalID=498&categoryID=14))
 * 
 * @package		CodeIgniter Amazon API
 * @author		Martin Koch <martin@shackeluri.com>
 * @copyright	Copyright (c) 2010, Martin Koch
 * @license		GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
 * @link		http://www.shackeluri.com/code/amazon_api/
 * @version		Version 0.1
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Amazon API Class
 * 
 * @package		CodeIgniter Amazon API
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Martin Koch <martin@shackeluri.com>
 * @link		http://www.shackeluri.com/code/amazon_api
 * @version		Version 0.1
 */
class Amazon_API {
	const VERSION = 0.1;
	
	protected $PUBLIC_KEY = "XXXX";
	protected $PRIVATE_KEY = "XXXX"; 
	protected $ASSOCIATE_ID = "XXXX";
	protected $AWS_VERSION = "2009-10-01";
	
	protected $SERVER = "ecs.amazonaws.com";
	protected $URI = "/onca/xml";
	protected $METHOD = "GET";
	
	protected $RESPONSEGROUP = "Small, Images";
	protected $MERCHANTID = "Amazon";
	protected $BROWSENODES = "27";  // TRAVEL (http://docs.amazonwebservices.com/AWSEcommerceService/2006-06-28/ApiReference/USBrowseNodesArticle.html)

	protected $SEARCHINDEX = "Books";
	protected $SORT = "salesrank";
		
	/**
	 * Constructor
	 * 
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		log_message('debug', 'Amazon_API Class Initialized');
	}
	
	/**
	 * 
	 * @access	public
	 * @param	string comma separated keywords REQUIRED
	 * @param	string title search NOT IMPLEMENTED
	 * @return	void
	 */
	function getBooks($keywords = FALSE, $title = FALSE){
		// build base url
		$AmazonQuery = array(
			"Keywords"	=> $keywords,
			"ResponseGroup" => $this->RESPONSEGROUP,
			"MerchantId" => $this->MERCHANTID, 
			"BrowseNodes" => $this->BROWSENODES,
			"SearchIndex" => $this->SEARCHINDEX,
			"Sort" => $this->SORT
		);
		
		// get secure url
		$url = $this->secureURL($AmazonQuery);
		echo($url);
		// init curl with secure url
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);

		$xml = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		$AmazonXML = new SimpleXMLElement($xml);
		return($AmazonXML);
	} 
	
	/**
	 * Generate secure url
	 * 
	 * @access	public
	 * @param	array	query
	 * @return	void
	 */
	function secureURL($query) {
		// urlencode the passed-in parameters
		// $qa will contain full URL pieces (param=value)
		$qa = array();
		foreach($query as $key=>$val) {
			$qa[$key] = rawurlencode($key) . "=" . rawurlencode($val);
		}

		//add parameters common to all requests
		$qa["AssociateTag"] = "AssociateTag=" . $this->ASSOCIATE_ID;
		$qa["AWSAccessKeyId"] = "AWSAccessKeyId=" . rawurlencode($this->PUBLIC_KEY);
		$qa["Service"] = "Service=AWSECommerceService";   
		$qa["Operation"] = 'Operation=ItemSearch';
		$qa["Timestamp"] = "Timestamp=" . rawurlencode(gmdate("Y-m-d\TH:i:s\Z"));
		$qa["Version"] = "Version=".$this->AWS_VERSION;

		//sort the query parameters before continuing
		ksort($qa);

		// generate the query string (after the ?)
		$querystring = implode("&", $qa);

		//generate the signature
		$sig = base64_encode(
			hash_hmac("sha256",
			"{$this->METHOD}\n{$this->SERVER}\n{$this->URI}\n{$querystring}",
			$this->PRIVATE_KEY,
			true)
		);
		//put it all together
		$url = "http://{$this->SERVER}{$this->URI}?{$querystring}&Signature=" . rawurlencode($sig);
		return($url);
		
	}
}