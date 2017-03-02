# forums
This contains setup scripts, instructions and code for using WordPress as a discussion forum in EdX courses.

It handles LTI authentication from EdX, which  allows one-click navigation from an EdX course into the discussion forum.  There's also a separate admin login that allows WordPress admin users to sign in directly.

For additional information, configuration for MIT Teaching Systems Lab courses, or commit and pull request history, see the MIT-internal repo at [private-teaching-systems-lab/forums](https://github.mit.edu/private-teaching-systems-lab/forums).  If your interested in trying this out yourself, feel free to reach out at [@mit_tsl](https://twitter.com/mit_tsl).

Alternately, you may be interested in a similar project using [Discourse](https://github.com/mit-teaching-systems-lab/discourse-edx-lti) as a discussion forum for EdX courses.

## Initial setup and course authoring
- [Initial setup for a new course](docs/initial-setup.md)
- [Course authoring](docs/course-authoring.md)

## Deploying fixes and improvements
#### Deploying
You can deploy changes to WordPress code with this script:
```
scripts/wordpress_deploy.sh $USER_HOST_TARGET
```

This deploys the code and activates plugins and themes.

If you are using a CDN for assets, this will not upload assets to the CDN immediately.  See the notes in [initial-setup.md](inital-setup.md) about using W3 Total Cache.

#### Updating WordPress
To update WordPress itself, run this locally:
```
scripts/update_wordpress.sh
```

This will not update any themes or mu-plugins.

To update WordPress plugins, run this locally:
```
scripts/update_plugins.sh
```

#### Extensions, plugins and themes
Global changes for the forums course are defined in `forums-config.php`, which is imported by `wp-config.php`, which is otherwise standard.

Most plugins and themes are off-the-shelf, while others have been heavily modified.  You can confirm this by running a `diff` on the code in source and a fresh download of the plugin, or by running `scripts/update_plugins.sh` and looking at the diff.

Modified plugins are:
- [themes/wp-knowledge-base-child/](blog/wp-content/themes/wp-knowledge-base-child)
- [mu-plugins/IMSBasicLTI.php](blog/wp-content/mu-plugins/IMSBasicLTI.php)
- [mu-plugins/LTI_Tool_Provider/](blog/wp-content/mu-plugins/LTI_Tool_Provider/)
- [Get Shopped Support Forums](blog/wp-content/plugins/bbPress-Support-Forums-master) ([function](https://github.com/mit-teaching-systems-lab/wordpress-edx-forums/blob/69f2d3d830fe7dadc3f7421b1e828bb4e2d71912/blog/wp-content/plugins/bbPress-Support-Forums-master/includes/bbps-user-ranking-functions.php#L34))

Some plugins and themes are used but no longer maintained upstream:
- [GD bbPress Widgets](blog/wp-content/plugins/gd-bbpress-widgets)
- [Get Shopped Support Forums](blog/wp-content/plugins/bbPress-Support-Forums-master)

#### CSS workarounds
See [custom.css](custom.css) and the Appearance > Simple Custom CSS plugin

## Monitoring
- [AWS CloudWatch Dashboard](https://us-west-2.console.aws.amazon.com/cloudwatch/home?region=us-west-2#dashboards:name=launching-innovation-dashboard)
- [AWS CloudFront Dashboard](https://console.aws.amazon.com/cloudfront/home?region=us-west-2#viewers_reports:)
- [forums-notifier](https://github.com/mit-teaching-systems-lab/forums-notifier) drops messages in Slack when Mailgun errors occur.
- [csp-logger](https://github.com/mit-teaching-systems-lab/csp-logger) for monitoring CSP violations
- [Rollbar](https://rollbar.com/) reports PHP and JS errors.  Additionally, a bit of JS added to the [wp-knowledge-base-child](blog/wp-content/themes/wp-knowledge-base-child/functions.php#3) checks for broken image links and reports them.
- [Production integration test](test) - Setup: `npm install`, install PhantomJS, and then set variables in `tester_env.sh`. Run: `npm run tester`.  This will click through a few pages on the happy path, so requires content.