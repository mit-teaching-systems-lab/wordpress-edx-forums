# Course authoring
#### Set course admins
Grant superadmin and forum keymaster permissions.  Admin users have to sign in through EdX LTI first.
- WordPress admin UI:
  Site > Users > Edit
  Role: Administrator
  Forum Role: Keymaster

#### Enable BuddyPress Profile page
- via https://buddypress.org/support/topic/need-link-to-user-profile-on-main-menu/
- Site > Appearance > Menus
- Screen Options (upper right) > Buddy Press

#### Create menu
```
Navigate to Site > Appearance > Menu
Set menu to:
- Forums (static home page)
- Groups
- Profile (BuddyPress)
- Members
- Login|Logout (Login/Logout links v1.3.3)
Check "Primary menu" and Save
Verify that menu appears on top of site
```

#### Create sidebar
```
Site > Appearance > Widgets
  in both "General Sidebar" and "bbPress Sidebar"
    (bbPress) Forum Search Form
    Tag Cloud, with Topic Tags
    Save each widget
  Note that the Topic Tags widget won't appear until there are posts with tags
Custom CSS styling
  Appearance > Simple Custom CSS
  (paste values from ./custom.css)
Update profile fields
  Network > Users > Profile Fields
  Add "About Me" with some text like this:
  "Tell us a bit about yourself. Perhaps your profession, interests, or relevant skills. This will help people find others to work with in the course."
```

#### Allow users to delete attachments
Wordpress admin UI: GD bbPress Attachments
- Site -> Dashboard -> Plugins
- Click GD bbPress Attachments 'Settings'
- Under 'Deleting Attachments' change Administrators, Moderators and Author to 'Delete from Media Library'

#### Course authoring: Content
WordPress admin UI:
```
Create Forum hierarchy
  Site > Forums > New Forum
  Official Course Forums (Type: Category)
  Course Admin (Type: Category, Parent: Official Course Forums)
  Course Logistics (Type: Forum, Parent: Course Admin)
  etc.

Create front page
  Site > Pages > Add new
  Set the title of the page
  Click "Text" to edit the page's content to include the top-level forum:
    `[bbp-single-forum id=25]`
  Set Appearance > Customize > Static Front Page > (the front page you created)
```
