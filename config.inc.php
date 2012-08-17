<?php
	/**
	 * This configuration file is all you need to edit. It 
	 * will be used by hook.php to set up the git process and 
	 * post-git commands (modifying folder security for apache).
	 */


	$branches = array(
		array(
			"branchName"=>"stage" //The branch to deploy. 'stage' branch used on staging server, dev for dev, prod for prod servers.
			,"branchTitle"=>"staging" //just used in logging
			,"gitFolder"=>"/var/www/MyWebsite" //the folder for the site that we're deploying
			,"gitURL"=>"https://github.com/ajbogh/GitHubHook" //the remote URL of the Git project
			,"allowedEmails"=>array("ajbogh@allanbogh.com") //optional, or can be blank array
		)
	);
	
	$githubIPs = array('207.97.227.253', '50.57.128.197', '108.171.174.178'); //an array of IPs that can run the deployment
?>