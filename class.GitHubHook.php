<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * GitHub Post-Receive Deployment Hook.
 *
 * @author Chin Lee <kwangchin@gmail.com>
 * @copyright Copyright (C) 2012 Chin Lee
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 1.0
 */

class GitHubHook
{
  /**
   * @var string Remote IP of the person.
   * @since 1.0
   */
  private $_remoteIp = '';

  /**
   * @var object Payload from GitHub.
   * @since 1.0
   */
  private $_payload = '';

  /**
   * @var boolean Log debug messages.
   * @since 1.0
   */
  private $_debug = FALSE;

  /**
   * @var array Branches.
   * @since 1.0
   */
  private $_branches = array();

  /**
   * @var array GitHub's IP addresses for hooks.
   * @since 1.1
   */
  private $_ips = array();

  /**
   * Constructor.
   * @since 1.0
   */
  function __construct() {
    /* Support for EC2 load balancers */
    if (
        isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
        filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)
      ) {
      $this->_remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $this->_remoteIp = $_SERVER['REMOTE_ADDR'];
    }

    if (isset($_POST['payload'])) {
      $this->_payload  = json_decode($_POST['payload']);
    } else {
      $this->_notFound('Payload not available from: ' . $this->_remoteIp);
    }
  }

  /**
   * Centralize our 404.
   * @param string $reason Reason of 404 Not Found.
   * @since 1.1
   */
  private function _notFound($reason = NULL) {
    if ($reason !== NULL) {
      $this->log($reason);
    }

    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found.';
    exit;
  }

  /**
   * Enable log of debug messages.
   * @since 1.0
   */
  public function enableDebug() {
    $this->_debug = TRUE;
  }

  public function addGitHubIPs($ipArr){
        $this->_ips = array_merge($this->_ips,$ipArr);
  }

  /**
   * Add a branch.
   * @param string $name Branch name, defaults to 'master'.
   * @param string $title Branch title, defaults to 'development'.
   * @param string $path Relative path to development directory, defaults to '/var/www/'.
   * @param array $author Contains authorized users' email addresses, defaults to everyone.
   * @since 1.0
   */
  public function addBranch($branchArrElem){ //$name = 'master', $title = 'development', $path = '/var/www/', $author = array()){
    $this->_branches[] = $branchArrElem;
    
    /*array(
      'name'   => $name,
      'title'  => $title,
      'path'   => $path,
      'author' => $author
    );*/
  }


  /**
   * Log a message.
   * @param string $message Message to log.
   * @since 1.0
   */
  public function log($message) {
    if ($this->_debug) {
      file_put_contents('log/hook.log', '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL, FILE_APPEND);
    }
  }

  /**
   * Deploys.
   * @since 1.0
   */
  public function deploy() {
    if (in_array($this->_remoteIp, $this->_ips)) {
      foreach ($this->_branches as $branch) {
      	//remove http:// and https:// from the URL. We don't really care about this.
      	if(preg_replace('/(https?):\/\//', "", $this->_payload->repository->url) == preg_replace('/(https?):\/\//', "", $branch["gitURL"])){
      		$this->log("");
      		$this->log("Beginning deployment...");
      		$this->log("Deploying ".$this->_payload->repository->url);
	      	$this->log($this->_payload->ref."==".'refs/heads/' . $branch['branchName']);
	        if ($this->_payload->ref == 'refs/heads/' . $branch['branchName']) {
	          $this->log('Deploying to ' . $branch['branchTitle'] . ' server');
	          
			  $dir = getcwd();
			  chdir($branch['gitFolder']);
	          $output = trim(shell_exec('/usr/bin/git pull origin '.$branch['branchName'].' 2>&1'));
			  shell_exec('/bin/chmod -R 755 .');
			  chdir($dir);
	          $this->log($output);
			  $this->log("");
	        }
		}
      }
    } else {
      $this->_notFound('IP address not recognized: ' . $this->_remoteIp);
    }
  }
}

