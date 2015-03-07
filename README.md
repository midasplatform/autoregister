# Midas plugin to automatically add users to be members of a selected list of communities.

This module provides the ability to automatically add all users as members to a set of targeted communities.

## Default behavior for new Communities

When the module is activated, an initial default setting is created and set to `'false'`, this setting controls whether new communities will be added to the autoregister target list.  This value is displayed in the checkbox in the module admin config page.  

If this value is `'false'` (unchecked), then when a new community is created, it will not be added to the autoregister target list, and no users will be automatically added as members.

If this value is `'true'` (checked), then when a new community is created, it will be added to the autoregister target list, and all users will be automatically added as members at that time.


## Moving Communities between the Targeted and Ignored Lists

In the Admin Config page of the module, a community can be moved from the autoregister targeted list to the autoregister ignored list, and back again.

When a community is moved to the autoregister targeted list, all existing users will be added as members of that community.  Any new users that are created will automatically be added as members to all communities on the autoregister targeted list.

When a community is moved to the autoregister ignored list, no existing users will be removed as members of that community.  Any new users that are created will not automatically be added as members to any communities on the autoregister ignored list.

## When a New User is added to the Site

The user will be added as a member of any community in the autoregister targeted list.

## When a New Community is added to the Site

If the default behavior for new communities is `'true'`, the community will be added to the autoregister targeted list and all existing users will be added as members to the community.

If the default behavior for new communities is `'false'`, the community will be added to the autoregister ignored list and no existing users will be added as members to the community.

## When a Community is Deleted

It will be removed from the autoregister list it is in, whether ignored or targeted.

