<?php
require_once('class.GitHubHook.php');
require_once('config.inc.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initiate the GitHub Deployment Hook
$hook = new GitHubHook;

// Enable the debug log, kindly make `log/hook.log` writable
$hook->enableDebug();
$hook->addGitHubIPs($githubIPs);

foreach($branches as $val){
	$hook->addBranch($val);
}

// Deploy the commits
$hook->deploy();

?>