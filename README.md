# Amos Community 

Communities are network of people having common purposes/interest. 
Common contents can be shared with community members and are available in community dashboard.
Community contents visibility for non-members depends on table fields contents_visibility that is 0 by default (contents are not available for non-members).

By default a community can be of type:
- Open: any user can subscribe (community visible in community list)
- Private: access reserved to user accepted by community managers or invited (visible but acceptance is required)
- Restricted to members (closed)- Subscription is only on invitation: community is visible only to community membership.

SubCommunities can be created under another community domain. 

### Installation

Add community requirement in your composer.json:
```
"open20/amos-community": "dev-master",
```

Enable the Community modules in modules-amos.php, add :
```
 'community' => [
	'class' => 'open20\amos\community\AmosCommunity',
 ],

```
add community migrations to console modules (console/config/migrations-amos.php):
```
'@vendor/open20/amos-community/src/migrations'
```

The community is suitable to be used with cwh as network.
To do so:
- Activate cwh plugin
- Open cwh configuration wizard (admin privilege is required) url: <yourPlatformurl>/cwh/configuration/wizard
- search for community in network configuration section
- edit configuration of community if needed and save

If tags are needed enable this module in "modules-amos.php" (backend/config folder in main project) in tag section. After that, enable the trees in tag manager.


### Configurable fields 

Here the list of configurable fields, properties of module AmosCommunity.
If some property default is not suitable for your project, you can configure it in module, eg: 

```php
 'community' => [
	'class' => 'open20\amos\community\AmosCommunity',
	'showSubcommunities' => false, //changed property (default was true)
 ],
 
```
* **showSubcommunities** - boolean, default = true  
Define if subcommunities are visible in the lists (created by, my communities, etc..)
* **showSubcommunitiesWidget** - boolean, default = false  
Define if the widget of subCommunities is visible in the community dashboard  
* **bypassWorkflow** - boolean, default = false  
If ignore community workflow  
* **enableWizard** - boolean, default = true  
If thew izard for community creation is enabled  
* **communityType** - int, default = null  
null if all community types are enabled, to have a fixed community type set this field  
to change default, use constants in Community type model, eg:
```php
'community' => [
    'class' => 'open20\amos\community\AmosCommunity',
    'communityType' => \open20\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED,
],
```
* **viewTabContents** - boolean, default = true  
Define if tab contents in community view mode is visible  
* **extendRoles** - boolean, default = false  
If true additional roles Author and Reader are considered, participant will be editor
* **customInvitationForm** - boolean, default = false  
If true associate or create user.

* **communityRequiredFields** - array, default = ['name', 'community_type_id', 'description']  
Mandatory fields in community form: by default, community name, type and description are mandatory.  
If in your platform, for example, you don't want community description to be a mandatory field, overwrite communityRequiredFields property as below:
```php
'community' => [
    'class' => 'open20\amos\community\AmosCommunity',
    'communityRequiredFields' => ['name', 'community_type_id']
],
```
* **hideContentsModels** - array, default = [(ClassPath)ShowcaseProject, (ClassPath)EenPartnershipProposal',(ClassPath)Event']  
Define the models class path to hide in view of content tab, overwrite hideContentsModels property as below:
```php
'community' => [
    'class' => 'open20\amos\community\AmosCommunity',
    'communityRequiredFields' => [
        'model/class/path',
    ]
],
```
* **inviteUserOfcommunityParent** - boolean, default = false  
You can invite user in a subcomunity only if they belogs to the community father

* **hideWidgetGraphicsActions** - boolean, default = false  

* **htmlMailContent** - array, default = []
  You can personalize the email sent by the community
  the values are present in getNumTypeEmail($type) in EmailUtility and are 
  ('registration-notification', 'registration-request', 'invitation', 'accept-invitation', reject-invitation, 'registration-rejects', 'welcome', 'change-role' )
```php
 'htmlMailContent' => [
        'welcome' => '@backend/mail/community/welcome'
        'change-role => '@backend/mail/community/change-role', //CHANGE_ROLE
    ],
```
* **htmlMailSubject** - array, default = []
It work in the some  way of the previous param

* **enableUserJoinedReportDownload** - boolean, default = false
Enable to display the "User Reports" container in community view (this will also display the "download user joined report" button inside it)

* **enableConfigureCommunityDashboard** - boolean, default = false
Added possibility to configure the widgets the are contained inside the community dashboard


* **autoCommunityManagerRoles** - array, default = []
All the users with the platform roles in this array, when creating a community, are added as community managers.

