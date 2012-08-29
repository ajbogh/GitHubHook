## GitHub Post-Receive Deployment Hook

Deploying applications to development, staging and production never been so easy with GitHub Post-Receive Deployment Hook script!

### Installation

Clone the script:

<pre><code>$ <strong>git clone https://github.com/ajbogh/GitHubHook.git</strong>
</code></pre>

Go to your `GitHub repo` &gt; `Admin` &gt; `Service Hooks`, select `Post-Receive URLS` and enter your hook URL like this:

![GitHub Post-Receive URLs](http://s3.kcblog.net/images/GitHubHook-01.png)

### How It Works

GitHub provides [Post-Receive Hooks](http://help.github.com/post-receive-hooks/) to allow HTTP callback with a HTTP Post. We then create a script for the callback to deploy the systems automatically.

You will need to create branches like `stage` and `prod` in Git before proceeding into the configuration.

Then open `config.inc.php`. An example is provided to illustrate how simple the configurations are.

<pre><code>&lt;?php
$branches = array(
		array(
			"branchName"=>"stage" //The branch to deploy. 'stage' branch used on staging server, dev for dev, prod for prod servers.
			,"branchTitle"=>"staging" //just used in logging
			,"gitFolder"=>"/var/www/MyWebsite" //the folder for the site that we're deploying
			,"gitURL"=>"https://github.com/ajbogh/GitHubHook" //the remote URL of the Git project
			,"allowedEmails"=>array() //optional, or can be blank array
		),
		array(
			"branchName"=>"prod" //The branch to deploy. 'stage' branch used on staging server, dev for dev, prod for prod servers.
			,"branchTitle"=>"PROD" //just used in logging
			,"gitFolder"=>"/var/www/MyWebsitePROD" //the folder for the site that we're deploying
			,"gitURL"=>"https://github.com/ajbogh/GitHubHook" //the remote URL of the Git project
			,"allowedEmails"=>array("ajbogh@allanbogh.com") //optional, or can be blank array
		)
	);
	
	$githubIPs = array('207.97.227.253', '50.57.128.197', '108.171.174.178'); //an array of IPs that can run the deployment
?&gt;
</code></pre>

We have a staging site and a production site in this example. You can add more branches easily by adding additional configuration arrays if you have more systems to deploy.

Set the owner of the website's directory to apache:

<pre><code>$ sudo chown -R apache: /var/www/MyWebsite
</code></pre>

Create or duplicate the .ssh folder that you use for Apache to use:

<pre><code>$ sudo cp -R /root/.ssh /var/www/
$ sudo chown -R apache: /var/www/.ssh
</code></pre>

Add a .htaccess to prevent unwanted visitors trying to access the .ssh folder.

*/var/www/.htaccess:*
<pre><code>RedirectMatch 404 ^/.ssh/.*$
</code></pre>

## 

### Security

Worried about security? An IP check is enabled to allow only GitHub hook addresses: `207.97.227.253`, `50.57.128.197`, `108.171.174.178` to deploy the systems. We also return a `404 Not Found` page when there is illegal access to the hook script.

For better security, add an email address to the configuration.

### For Developers

We are trying to make developers' lives easier. Kindly fork this on GitHub and submit your pull requests to help us.
