# Changelog

## 2.3
- adds a command to rewrite public urls in the menu to internal urls ("unalias" them)

## 2.5.0
- Implemented new UriVoter to fix BC break issue in KnpMenuBundle 2.x where voters are used to determine active state. This is a backwards compatible fix whereas the voter is only used when 2.x of the KnpBundle is used.

## 2.6.0
- Add a possibility to hint names of menus to be preloaded in the `zicht_menu` config.
  This reduces the number of queries to a maximum of 3 for all menus that are common to a lot of pages.

## 2.6.1  -  Tue, 04 Oct 2016 08:25:44 GMT
- lower dependency of doctrine extensions
- Allow getItemBy ':level' property

## 2.6.2  -  Mon, 10 Oct 2016 13:45:02 GMT
- Prevent crash when preloaded menus is not set

## 2.6.3  -  Thu, 13 Oct 2016 15:48:48 GMT
- made "compatibility fix" actually compatible
- Merge branch '2.4.x' into 2.5.x
- Merge branch '2.5.x' into 2.6.x

## 2.7.0  -  Fri, 28 Oct 2016 15:57:31 GMT
- Allow proper builder override *was broken*

## 2.7.1  -  Fri, 04 Nov 2016 12:45:03 GMT
- Allow framework extra bundle version 5

## 2.8.0  -  Wed, 30 Nov 2016 14:17:38 GMT
- fixes issue where the flush triggered a flush for other items than the items managed by the menu manager
- [DON-282] URI should be null when there is no path
  
  The bug in Donner was caused by the URI defaulting to '/' when the
  path (in the database) was null.  This is not intended.

- remove compiler pass (it doesn't need to be done after build)
- restore 'preload menus', fix `json_data` deserialization
- add menu voter to check menu object with name
- fix current menu item callback

## 2.8.1  -  Thu, 01 Dec 2016 13:00:15 GMT
- be defensive on non-existent menu's

## 2.8.2 Wed, 25 Jan 2017 09:54:20 GMT
- fix call to is_callable: it expects an array as first argument.

## 2.8.3  -  Wed, 22 Feb 2017 11:32:03 GMT
- default config (no builder_service) was broken

## 2.8.4  -  Tue, 14 Mar 2017 13:39:11 GMT
- add license
- be defensive on non-existent menu's
- fix call to is_callable: it expects an array as first argument.
- Add status provider to check the state of the NestedTree

## 2.8.5
- update readme
- hotfix in MenuItemPersistenceSubscriber. Checking if a given property exists in the form.

## 2.8.6
- return null if root is empty

## 2.8.7
- symfony 2.8 fixes
- symfony 3.4 fixes
- `getBlockPrefix` to replace `getName`
- dependencies update 

## 2.8.8  -  Wed, 8 Mar 2018 13:40 GMT
- Refactored the default fallback from `[none]` to `%kernel.default_locale%` to make behavior valid in donner 3.0.
- Updated previous missing changelog entries

## 2.8.10
- bug fix for the `duplicate on tree root` bug when removing pages (with linked menu items)  