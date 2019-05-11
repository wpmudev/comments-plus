# Comments+
#

**INACTIVE NOTICE: This plugin is unsupported by WPMUDEV, we've published it here for those technical types who might want to fork and maintain it for their needs.**

## Translations

Translation files can be found at https://github.com/wpmudev/translations

## Comments + allows readers to comment on your posts using their Facebook, Twitter, Google+ or WordPress.com accounts.

 

![Give users more ways to participate with Comments +.](https://premium.wpmudev.org/wp-content/uploads/2011/09/comments-plus-735x470.jpg)

 Give users more ways to participate with Comments +.

### Create a Social Buzz

Comments + adds four easy ways to participate in conversations on your site with no extra usernames or passwords to remember. By default, users will share posts they comment on to Facebook or Twitter, putting your content in front of more people. 

### Added Functionality

The 12 included add-ons make it easy to extend functionality. Add MailChimp list integration, Twitter Mention to include your handle in tweeted posts and include post featured images when sharing to Facebook. Plus, all the tools you need to make sure Comments + runs smooth on any site configuration.  

![Simple toggle controls for fast configuration.](https://premium.wpmudev.org/wp-content/uploads/2011/09/facebook-735x470.jpg)

 Simple toggle controls for fast configuration.

### Extra Flexibility

Simple toggle controls let you set the services you want to make available, adjust the display order and choose a prefered default service. Seamlessly integrate to fit your theme or use built-in hooks for custom styling. 

## Usage

For help with installing plugins please refer to our [Plugin installation guide](https://premium.wpmudev.org/wpmu-manual/installing-regular-plugins-on-wpmu/). 1\. Install the Comments Plus Plugin. 2.  Navigate to **Plugins** and activate the plugin:

*   You can activate on a site by site basis (via **Plugins** in the site admin dashboard) or network activate it (via **Plugins** in the network admin dashboard) on Multisite
*   You would **NOT** network activate this plugin if any of your sites use domain mapping.

### To Configure:

To begin with, the plugin respects default comment settings in WordPress, located at **Settings** > **Discussion**.

*   Ticking _Users must be registered and logged in to comment_ will require login/registration for comments to occur.
*   Un-ticking _Users must be registered and logged in to comment_ will allow guests (logged-out or unregistered users) to comment.

Visit the Plugin configuration page at **Settings > Comment Plus** to set up.

*   You can configured for your entire network when network activated via **Settings > Comments Plus** in the network admin dashboard
*   All Comments Plus Add Ons are activated on a site by site basis via **Settings > Comments Plus** in the site admin dashboard

#### Facebook App Info

You must make a Facebook app to start using Comment Plus. The Facebook app is required so commenters can grant permission for your website to authenticate against their Facebook login. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus61.jpg)

 Creating a Facebook app is really easy! **Here's how you do it:** 1.  Go to the [Facebook developers app](https://developers.facebook.com/apps) page (you'll need to log in using your Facebook account). 2.  Once logged in clicked on **Go To App** and then **Create New App**. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus62.jpg)

   3.  Give your app name and click **Continue.**

*   _TIP_ - Best to use a name people who comment on your site can identify with as this is what they'll see when they click to login with Facebook.

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus63.jpg)

 4.  Enter the security check and click **Submit** (if your Facebook account has not been verified you will need to verify it by adding a mobile phone number or credit card). 5.  Add your App domain. 6.  Click on Website, add your Site URL and click **Save Changes**

*   _Tip_:  Click on **Edit Icon** if you want to upload your own icon.

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus64.jpg)

 7.  Now go to **Settings > Comment Plus**, add your App ID and App Secret under Facebook App info and click **Save Changes**.

*   Select 'I want to use this app on all subsites too' if network activated and want to use on all sites.
*   DO NOT select 'I want to use this app on all subsites too'  if any sites are using Domain mapping.

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus65.jpg)

#### Twitter App Info

You must make a Twitter app to start using Comment Plus. The Twitter app is required so commenters can grant permission for your website to authenticate against their Twitter login. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus66.jpg)

 Creating a Twitter app is really easy! **Here's how you do it:** 1.  Go to the [Twitter developers app](https://dev.twitter.com/apps/new) page (you'll need to log in using your Twitter account). 2.  Add a Name, a Description, webs site, callback URL and then click **Create Your Twitter application**.

*   _TIP_ - Best to use a name and description people who comment on your site can identify with as this is what they'll see when they click to login with Twitter.

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus68.jpg)

 3.  Now click on **Create my access token.** 4\. Next click on **Settings** tab. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus69.jpg)

 5.  Select **Read and Write** and then click on **Update this Twitter application's settings**. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus70.jpg)

 6.  Now go to **Settings > Comment Plus**, add your Consumer key and Consumer Secret under Twitter App info and click **Save Changes**.

*   Select 'I want to use this app on all subsites too' if network activated and want to use on all sites.
*   DO NOT select 'I want to use this app on all subsites too'  if any sites are using Domain mapping.

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus71.jpg)

#### General Settings

WordPress branding and options allows you to change the default WordPress logo to your network logo. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus72.jpg)

 'Do not show Comments With...' lets you control which comment login options are included. 

![image](https://premium.wpmudev.org/wp-content/uploads/2011/09/commentsplus73.jpg)

#### Comment Plus add-ons

Comments Plus includes scores of add-ons to extend it's basic features. They can be activated through the settings page at **Settings > Comments Plus**. Here's a quick overview of the add-ons:

Alternative Facebook Initialization

Activate this add-on to solve some of the Facebook javascript initialization conflicts with other plugins.

Custom Comments Template

Using the custom template will override your themes default setup. We provide various default styles you can select from. You may also use those CSS styles within your own theme (found here `/plugins/comments-plus/css/themes/`) or you can simply move `lib/forms/wdcp-custom_comments_template.php` to your themes folder.

Troubleshooter

Activate this add-on to troubleshoot possible configuration issues.

Facebook Locale

Forces your selection for localized Facebook scripts. By default, the plugin will try to auto-detect your locale settings and load the appropriate API scripts. With this add-on, you can explicitly tell which language you want to use for communicating with Facebook.

Facebook Featured post image

Forces featured image to always show next to the posts on Facebook instead of relying on defaults. Also allows you to choose the image(s) used when there is no featured image available.

MailChimp List Subscription

Adds a checkbox to facilitate subscribing your commenters to your existing MailChimp list.

Services order

Allows you to re-order the services tabs.

Social Discussion

Synchronizes the relevant discussion from social networks in a separate tab on your page. Requires _Custom Comments Template_ add-on to be activated.

Fake Twitter Email

Twitter doesn't allow access to users' emails, which may cause comment approval issues with WordPress. This add-on will associate unique fake email addresses with Twitter commenters to help with this issue.

Link to Twitter profile

Enabling this addon will force the plugn to always use Twitter profile URLs as websites for your Twitter commenters, regardles of their Twitter profile settings.

Mention me

Adds a pre-configured Twitter username to all messages posted to Twitter. You can set up the username in plugin settings.

Short URLs

Integrates an URL shortening service call to your outgoing URLs.
