## Initial setup
This will lead you through setting up a new WordPress forum site for an EdX course.  This includes:

- Creating an RDS database
- Creating and setting up a WordPress instance on EC2
- Setting up LTI login through EdX and separate WordPress admin login
- Setting up MailGun so WordPress can send emails
- Adding error monitoring through Rollbar
- Using S3 and CloudFront as a CDN
- Adding additional WordPress instances for reliability or horizontal scaling
- Add CloudWatch dashboard and alerts

It requires using several tools, particularly pieces of AWS.  If you're familiar with these services, the process will probably still take a few hours.  While some steps have been automated or turned into scripts, much of this process requires using GUI tools.

It's recommended that you cut a long-lived branch for each course (eg., `edtech-forums-master`) while working.  There are only a handful of code changes needed for each course, but this can keep them distinct, and support rebasing onto the mainline master to get fixes that apply to each course.  You can search for these code changes by grepping for `MITTSL_UPDATE_THIS_VALUE`.

#### Create AWS instance
AWS UI:
- Create RDS database
- Create keypairs for sshing into instance
- Launch a fresh Ubuntu 16 image in EC2

#### Set security groups
- RDS should only be accessible by WordPress EC2 security group
- During setup, it may be helpful to keep WordPress instances open to the world for debugging
- For production, WordPress instances should only be accessible by ELB security group (or over SSH)

#### Create local config.sh
Create a new config.sh file (not checked into source), that contains
secrets about the database instance and admin WordPress user to create.

#### Install dependencies
Local shell:
```
USER_HOST_TARGET=ubuntu@52.1.2.3 # point to your instance
scripts/remote_scripts_deploy.sh $USER_HOST_TARGET
ssh $USER_HOST_TARGET '~/remote/provision.sh'
```

#### Configure and deploy nginx
Local filesystem:
- Edit `nginx/nginx.conf` for `server_name` directive
- Edit `nginx/wordpress.conf` to update whitelisted domains allowed by Content-Security-Policy
- See [csp-logger](https://github.com/mit-teaching-systems-lab/csp-logger) for a service that can track CSP violations
```
scripts/nginx_deploy.sh $USER_HOST_TARGET
```

You can test this is working correctly by curling /health from the instance and seeing activity in `/var/log/nginx/access.log`, or from curling it from your local machine by IP.

#### Add instance to load balancer
AWS UI:
- Create new classic ELB
- Set health check route to /health and set healthy and unhealthy thresholds to 2
- Add a wildcard SSL certificate (created separately through AWS Certificate Manager)
- Add your EC2 instances
- Set application stickiness for your particular WordPress cookie (wildcards don't appear to work).  You may need to come back to this later when it's time if you're having trouble with logging into WordPress the first time.
- In Route 53, add a CNAME record for the subdomain that points to the ELB

Check this with `dig`, in the AWS ELB monitoring UI, and finally by curling /health at the full domain from your machine.  It may take a few minutes for the DNS changes to propagate.

#### Deploy WordPress code
To deploy:
```
scripts/wordpress_deploy.sh $USER_HOST_TARGET
```

This should end with:
```
Error: 'wp-config.php' not found.
Either create one manually or use `wp core config`.
WordPress not installed; plugins and themes not updated.
```

This is as expected.  The WordPress code is now on the box, but it won't work yet since the database isn't configured yet.

#### Warning: Rebuild database and make new WordPress site
If you do this while pointing to an existing database (eg., when adding another instance), it will destroy the existing site.

On local filesystem:
- Edit `config.sh` for WordPress setup (not checked into source)

Local shell:
```
ssh $USER_HOST_TARGET '~/remote/new_wordpress.sh'
```

This uses [wp-cli](http://wp-cli.org/) to drop and re-create the database, and create a new WordPress site.  If this command appears to hang, check the security group setup to ensure that the EC2 instance can reach the RDS instance.

In the browser:
Visit the site!  You should see a hello world page, although the site won't look the way you expect yet.

Click `Login` and use the credentials for the WordPress admin user that you put in `config.sh`.  If you're having problems with redirect loops, look at the cookies that the response are trying to set, and check that those cookies are sticky in the ELB setup.


#### LTI setup, part 1 of 2
- Generate a consumer key and secret
- EdX Studio > Course Settings > Advanced
  Advanced Module List > Add `lti`, `lti_consumer`
  Add LTI Passport with `name:key:secret`
- EdX Studio > Forum page
  Add LTI integration
  Email integration requires:
    LTI Launch Target: New Window
    Request user's email: True
- Local filesystem:
  Update forums-config.php for MY_EDX_URL, we'll deploy that change shortly.
- WordPress admin UI: Set up LTI keys
  Network -> Settings -> LTI Consumers Keys
- WordPress admin UI: Add admin login page
  Site > Pages > Add new page named exactly "Admin login"
- Browser:
  Verify you can visit /admin-login in the browser (a normal page, not a login page yet)

#### Activate plugins and themes
Run the deploy script.  This will deploy the change to the EdX course URL, and will activate the WordPress themes and plugins.
```
scripts/wordpress_deploy.sh $USER_HOST_TARGET
```

You may see database error messages related to the tables `wordpressdb.wp_rt_rtm_media` and `wordpressdb.wp_bp_groups` not existing yet.  You can ignore those errors, we'll fix that up next.

Check that the site looks different, and closer to what you expect.  There's still steps to configure the look and feel, so it won't look exactly like other courses yet.

WordPress admin UI: BuddyPress
- Network -> Settings -> Buddypress
- Make all components active and save

Redeploy: `scripts/wordpress_deploy.sh $USER_HOST_TARGET`, and this should succeed with no database errors now that each BuddyPress component is active.


#### LTI setup, part 2 of 2
Finish LTI and basic plugin setup
- WordPress admin UI: Make site private
  Site -> Plugins -> Jonradio
  Settings page
  Check "Private Site"

Verify login:
- Browser:
  Open a separate browser session from the admin login (eg., an icognito window)
  Verify you are redirected to the proper EdX course when visiting /
  Verify LTI login through EdX works
  Visit /wp-login.php?action=logout and confirm, verify that logs you out
  Verify you see admin login page at /admin-login


#### Rollbar setup for error reporting
1. Create a new project on rollbar.com
2. In the WordPress Admin UI, navigate to Site > Tools > Rollbar
3. Check both PHP and JavaScript error logging
4. Add the server-side and client-side keys
5. Follow Rollbar instructions to verify that errors are reported


#### Enable sending email notifications
Emails are ultimately sent through an email service called MailGun.
- Mailgun: Sign up for the MailGun service
- Mailgun/AWS: Update DNS records to verify domain (see MailGun docs for help)
- WordPress admin UI: Site > Plugin > MailGun > Settings
- WordPress admin UI: Set MailGun to use HTTP, enter MailGun API Key, and `Test configuration`
- Mailgun: Verify logs in MailGun UI, and verify that you receive the test email


## Sync assets and uploads to CDN
This creates an S3 bucket to hold assets, so that they can be distributed to the CloudFront CDN, and so that they can be shared across multiple WordPress instances.  This involves setting up the S3 buckets for this, and creating a new IAM user for WordPress that will have access to push new assets into the bucket.

#### Create an S3 bucket
1. Create the bucket
2. Add bucket policy, replacing bucket name:
```
{
  "Version": "2008-10-17",
  "Statement": [
    {
      "Sid": "AllowPublicRead",
      "Effect": "Allow",
      "Principal": {
        "AWS": "*"
      },
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::YOUR_BUCKET_NAME_HERE/*"
    }
  ]
}
```
3. Set CORS configuration
Set `YOUR_WORDPRESS_DOMAIN_NAME`:
```
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
    <CORSRule>
        <AllowedOrigin>https://YOUR_WORDPRESS_DOMAIN_NAME</AllowedOrigin>
        <AllowedMethod>GET</AllowedMethod>
        <MaxAgeSeconds>3000</MaxAgeSeconds>
        <AllowedHeader>Content-*</AllowedHeader>
        <AllowedHeader>Host</AllowedHeader>
    </CORSRule>
</CORSConfiguration>
```
#### Enable logging for bucket
1. Create another bucket to hold the logs
2. On the asset bucket, enable logs in Permissions > Logging

#### Create an IAM user for WordPress
1. Create IAM user policy, replacing bucket name:
```
{
  "Version" : "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:ListBucket",
        "s3:GetBucketLocation",
        "s3:ListBucketMultipartUploads"
      ],
      "Resource": [
                   "arn:aws:s3:::YOUR_BUCKET_NAME_HERE"],
      "Condition": {}
    },
    {
      "Action": [
        "s3:AbortMultipartUpload",
        "s3:DeleteObject*",
        "s3:GetObject*",
        "s3:PutObject*"
      ],
      "Effect": "Allow",
      "Resource": [
                  "arn:aws:s3:::YOUR_BUCKET_NAME_HERE/*"
      ]
    },
    {
      "Effect": "Allow",
      "Action": "s3:ListAllMyBuckets",
      "Resource": "*",
      "Condition": {}
    },
 
    {
      "Action": [
      "cloudfront:ListDistributions",  "cloudfront:CreateDistribution"
      ],
      "Effect": "Allow",
      "Resource": "*"
    }
  ]
}
```
2. Create IAM user for WordPress to use to put updated assets in the bucket
3. Set policy to the one you just created
4. Generate credentials, that you'll set on the WordPress instances

#### Create a CloudFront distribution
1. Create distribution > Web
2. Pick your S3 bucket
3. Set Viewer protocol to HTTPS only
4. Set Allowed HTTP Methods to "GET, HEAD, OPTIONS" (for CORS)
5. In Forward Headers, Add `Origin` to Whitelist (for CORS)
6. Click create distribution
7. Wait until the status is "deployed", which may take up to 15 minutes

#### Set up W3 Total Cache
This will rewrite asset URLs to the CDN domain, and use UI tools to do an initial upload to push asset files to S3.

Go to My Sites > NetworkAdmin > Dashboard, Performance > General Settings.
You should see a banner towards the top that says `W3 Total Cache Error`.  This is because the plugin expects to be able to modify configuration on its own, but the permissions we use are stricter and prevent this.

Locally, add `define('WP_CACHE', true);` to `forums.config.php` and redeploy.

Next, show the instructions about the errors in the W3 Total Cache admin UI, they should look like this:

```
cp /var/www/html/blog/wp-content/plugins/w3-total-cache/wp-content/advanced-cache.php /var/www/html/blog/wp-content/advanced-cache.php
chmod 777 /var/www/html/blog/wp-content/cache
chmod 777 /var/www/html/blog/wp-content/w3tc-config
rm -rf /var/www/html/blog/wp-content/cache/tmp
```

Verify the error message is resolved in the admin UI.

#### Enable the CDN
- In Performance > General Settings
- Scroll down to CDN
- Check "Enable" and select Origin Push > Amazon Simple Storage Service (S3)
- Click “Save All Settings”

Navigate to Performance > CDN
- Add the access key and secret that you set up for the IAM user.
- Set it to always use SSL
- Use CloudFront domain (eg., abc123.cloudfront.net)
- Verify that "test" button succeeds, which tests the credentials, security policies and CloudFront setup.

Login to the forum site, and verify that it is now making requests for assets to the right domain and URL, even if those requests are being blocked by the CSP policy.

Update the nnginx configuration in `wordpress.conf` to add the specific CloudFront domain you just created.  Avoid using a wildcard for CloudFront domains, since that effectively allows arbitrary code to be injected.  Run `scripts/nginx_deploy.sh` to deploy that updated config and reload nginx.

#### Initial upload of assets to CDN
In the WP admin UI, navigate to Performance > CDN.

Use the buttons at the top of the page to upload all the assets.  If you already have multiple instances, and they already each have different assets, you may need to run this multiple times so that the assets from each instance are uploaded.

- export the media library
- wp-includes
- theme files (see note below)
- custom files (which include BuddyPress)

Note on theme files:
Because of the way the `wp-knowledge-base-child` theme is written, you also need to upload the `wp-knowledge-base` theme files.  In the WordPress admin UI, temporarily change the theme to `wp-knowledge-base`, upload the theme assets in the W3 total cache plugin UI, and then change back to the child theme.  Verify that these files are in the S3 bucket.  Verify that the theme assets all load in the site.

You may also see 307 redirects, which appears to be part of the warm-up process for a new S3 bucket (see [Stack Overflow](http://serverfault.com/questions/730958/error-response-was-307-temporary-redirect-when-trying-to-upload-to-buck)).  You can wait a bit and then invalidate all files in the CloudFront distribution (see [here](http://docs.aws.amazon.com/AmazonS3/latest/dev/Redirects.html), or alternately you can add the S3 domain to the CSP whitelist so that the site continues to work while CloudFront warms up.

#### Debugging issues and repairing assets
If you're having issues, check the `scripts` folder for some scripts that can be useful for verifying that files are being pushed to the CDN correctly, or for manually pushing some files from the local command line.  Keep in mind that uploads to S3 must also set metadata for Content-Type and Content-Encoding in order for CloudFront to serve them correctly to browsers.

I couldn't figure out why, but W3 Total Cache doesn't upload .woff font files, even though they are listed in the admin UI in the set of files to include.  To workaround this, you can use the `scripts/check_cdn.sh` script.  Set the credentials for the S3 upload user on the remote machine, then do a `--dryrun`.  You should see that some woff files are missing.  Run the command without `--dryrun` to sync them manually.  Verify that fonts load correctly on the forum site.

#### Verify user uploads
After the course is setup, you should come back and verify that user uploads on the site work as expected.  This should work even though "Host attachments" is unchecked and disabled in the W3 Total Cache admin UI.

If you see an error like "The uploaded file could not be moved to wp-content/uploads/2017/02" in the UI, look at `provision.sh` for the commands to grant ownership of these folders to WordPress.

#### Tighten permissions after setup
Run `chmod 755 /var/www/html/blog/wp-content` as suggested on the W3 Total Cache admin UI.


## Adding additional WordPress instances
For availability and scaling horizontally.

As a point of comparison, 11-154x ran with:
- RDS: t2.micro 
- EC2: t2.micro, 2 instances
- [Example CloudWatch metrics for 11.154x](docs/cloudwatch-11-154x.png)

#### Make an AMI
- AWS UI:
  Instances > Actions > Image > Create Image
- AWS UI:
  Images > AMI > Launch
- AWS UI:
  Load Balancing > Load Balancers
  Add the new instance to the ELB

If you need to scale up instances during a course, you can also launch the AMI with bigger instances that you initially used, and cycle the smaller ones out of service.  Alternately, you could set up auto-scaling groups, although that hasn't been necessary for the initial courses.

#### Update W3TC config on each host
Since the admin UI writes the config to disk, this needs to be updated across hosts manually.  The relevant file is `master.json`, and everything except for `common.instance_id` should be the same.  This is most important for the AWS credentials.  If you are updating or copying these files manually, keep in mind that the file contains AWS secrets.

### Verify setup
Verify that login/logout works correctly with sticky cookies, that requests for assets all go to the CDN, and that user uploads work correctly and are available from both instances.


## Add CloudWatch monitoring and alerts
A dashboard can be useful for checking things like:

- RDS CPU, free disk
- EC2 CPU, network throughput
- ELB latency, request rate
- S3 object count

And email alerts can be added for various thresholds (eg., CPU over 80%, storage under 10GB).