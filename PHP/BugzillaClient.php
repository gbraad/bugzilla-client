<?php
// Add search path for includes to the ini
//ini_set("include_path", "");

// Add the AutoLoader
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

class BugzillaClient
{
    // Initialize the client
    protected $rpcClient = null;
    protected $basUrl = null;

    public function __construct($baseUrl, $username, $password)
    {
	$this->baseUrl = $baseUrl;    // Store internal for later use
	$this->rpcClient = new Zend_XmlRpc_Client($baseUrl . "/xmlrpc.cgi");
    
	$httpClient = $this->rpcClient->getHttpClient();
	$httpClient->setCookieJar();  // Needed to retain user cookie
	$httpClient->setAuth($username, $password, Zend_Http_Client::AUTH_BASIC);

	// Login request (XMLRPC)
	$response = $this->rpcClient->call('User.login', array(array(
	    'login'    => $username,
	    'password' => $password,
	    'remember' => 1
	)));
    }

    public function __destruct()
    {
	// Logout request (XMLRPC)
	$response = $this->rpcClient->call('User.logout', array());  // Do logout
    }

    public function Get($id)
    {
	$returnValue = array();
    
	// Get request (XMLRPC)
	$response = $this->rpcClient->call('Bug.get', array(array('ids' => $id)));
	$index = 0; // Only retrieving one bug, so don't bother with index now

	$returnValue['id'] = $id;
	// Construct a remote URL
	$returnValue['url'] = $this->baseUrl . "/show_bug.cgi?id=" . $id;
	// Collect some basic information about the bug
	$returnValue['summary'] = $response['bugs'][$index]['summary'];
	$returnValue['status'] = $response['bugs'][$index]['status'];
	$returnValue['resolution'] = $response['bugs'][$index]['resolution'];

	// Comments request (XMLRPC)
	$response = $this->rpcClient->call('Bug.comments', array(array('ids' => $id)));
	// Extract the first comment from the bug and store as description
	$returnValue['description'] = $response['bugs'][$id]['comments'][0]['text'];

	return $returnValue;
    }
}
?>
