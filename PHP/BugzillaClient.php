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

    public function Get($bugIds)
    {
	// Get request (XMLRPC) and process results and include description
	return $this->processGet($this->rpcClient->call('Bug.get', array(array('ids' => $bugIds))), true);
    }

    public function Search($product, $status, $resolution, $summary, $whiteboard)
    {
	// Search request (XMLRPC)
	$response = $this->rpcClient->call('Bug.search', array(array(
	    //'product'    => $product,		// Does not seem to work for me
	    'resolution' => $resolution,
	    'status'     => $status,
	    'summary'    => $summary,
	    'whiteboard' => $whiteboard
	)));

	// Process results and do not include bug description
	return $this->processGet($response, false);
    }

    private function getDescription($bugId)
    {
	// Comments request (XMLRPC)
	$response = $this->rpcClient->call('Bug.comments', array(array('ids' => $bugId)));
	// Extract the first comment from the bug and store as description
	return $response['bugs'][$bugId]['comments'][0]['text'];
    }

    private function processGet($input, $includeDescription)
    {
	$returnValue = array();

	foreach($input['bugs'] as $bug)
	{
	    $bugId = $bug['id'];

	    // Construct a remote URL
	    $returnValue[$bugId]['url']         = $this->baseUrl . "/show_bug.cgi?id=" . $bugId;
	    // Collect some basic information about the bug
	    $returnValue[$bugId]['id']          = $bugId;
	    $returnValue[$bugId]['summary']     = $bug['summary'];
	    $returnValue[$bugId]['status']      = $bug['status'];
	    $returnValue[$bugId]['resolution']  = $bug['resolution'];
	    $returnValue[$bugId]['assigned_to'] = $bug['assigned_to'];

	    // Include first comment as description
	    if($includeDescription)
		$returnValue[$bugId]['description'] = $this->getDescription($bugId);
	}

	return $returnValue;
    }
}
?>
