# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Removed
- Removed PHP 8.0 from Github Q&A action
### Added|Changed|Deprecated|Removed|Fixed|Security
Nothing so far

## 6.1.1 - 2023-03-09
### Added
- Added `MenuItem::copy`

## 6.1.0 - 2022-12-16
### Added
- Forward compatability for `doctrine/dbal ^3`

## 6.0.3 - 2023-02-06
### Changed
- Forward merge of `5.1.1`

## 6.0.2 - 2022-11-16
### Added
- Extra MySQL-index for `MenuItem` where `lvl` and `language` are queried together

## 6.0.1 - 2022-11-11
### Fixed
- Adding `name` twice on `TreeAdminExtension`
- Return value for `PublicToInternalUriCommand`

## 6.0.0 - 2022-10-06
### Added
- Support for Symfony ^5.4
### Removed
- Support for Symfony 4
- Support for PHP 7.2/7.3

## 5.1.1 - 2023-02-06
### Changed
- Allow for html in `admin.help.menu_item_name` as it is sometimes desirable.

## 5.1.0 - 2022-10-03
### Added
- Support for zicht/admin-bundle v7 and zicht/url-bundle v6 = support for Sonata 4.
- Made own code forward compatible with Sonata 4.

## 5.0.5 - 2022-10-01
### Changed
- Introduced PHP CS Fixer.
- Removed PHP 7.3 and added PHP 8.1 to the GitHub actions PHP versions matrix.
- Additional cleanup.
- Remove suggests from composer.json

## 5.0.4 - 2022-05-20
### Changed
- Forward merge of v4.3.4: Require symfony/security-core instead of bundled package symfony/security

## 5.0.3 - 2022-05-03
### Fixed
- Pass root name to TreeBuilder constructor and call `getRootNode` instead of deprecated `->root()`

## 5.0.2 - 2022-04-12
### Fixed
- Forward merge of 4.3.3: addGhostItem tried to obtain ID and title trough array access on the object, but
  this will be looking for children 'id' and 'title' instead of the (extra) properties 'id' and 'title'.

## 5.0.1 - 2022-04-04
### Added
- Forward merge of 4.3.2: Added FQCN service aliases for auto-wiring

## 5.0.0 - 2022-03-09
### Added
- Support for `gedmo/doctrine-extensions^3`. This requires the following changes in your configuration

#### Before
```
doctrine:
  orm:
    tree:
      type: annotation
      alias: Gedmo
      prefix: Gedmo\Tree\Entity
      dir: '%kernel.project_dir%/vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Entity'
```
#### After
```
      dir: '%kernel.project_dir%/vendor/gedmo/doctrine-extensions/src/Tree/Entity'
```

## 4.3.4 - 2022-05-20
### Changed
- Require symfony/security-core instead of bundled package symfony/security

## 4.3.3 - 2022-04-12
### Fixed
- addGhostItem tried to obtain ID and title trough array access on the object, but this will be looking for
  children 'id' and 'title' instead of the (extra) properties 'id' and 'title'.

## 4.3.2 - 2022-04-04
### Added
- Added FQCN service aliases for auto-wiring

## 4.3.1 - 2022-02-16
### Changed
- Slightly optimized `Builder::loadRoots` to not repeatedly check on empty root-menu's for a given locale.

## 4.3.0 - 2021-12-13
### Added
- Support for Twig 3
### Removed
- Support for Twig < 2.7

## 4.2.2 - 2021-12-13
### Fixed
- Missing translation of form label `name` and CMS breadcrumbs.

## 4.2.1 - 2021-12-07
### Removed
- Support for `gedmo/doctrine-extensions^3` as it is a BC-break in disguise.

## 4.2.0 - 2021-12-02
### Added
- Support for PHP 8
### Removed
- Support for PHP 7.1

## 4.1.4 - 2021-04-30
### Fixed
- Do not expect return value from `parent::configureListFields()` in `MenuItemAdmin::configureListFields()`
- Forward merge from `3.0.6` including fixes for code updated in `4.1.1`

## 4.1.3 - 2021-01-08
### Fixed
- Translation for `form.label_name` replaced with specific one as it interferes with the normal usage of the "name" label.

## 4.1.2 - 2020-11-16
### Changed
- Introduced the missing dependency on `knplabs/knp-menu-bundle` and updated existing code to be compatible with version 3.

## 4.1.1 - 2020-11-02
### Fixed
- `ValidateNestedTreeProvider` now extends `StatusProviderHelper` so the `check` method can be utilized and this Provider
  no longer has its workload implemented in the `__construct`

## 4.1.0 - 2020-10-26
### Changed
- The `DatabaseMenuProvider` now expects a requestStack as its second argument instead of the whole Container and a matcher as its third argument which implements the `MatcherInterface`. This will be used to match the uri to set the current menu item.

## 4.0.2 - 2020-10-20
### Changed
- Introduce `Doctrine\Persistence\ManagerRegistry`, fixing deprecated use of `RegistryInterface`.

## 4.0.1 - 2020-09-21
### Added
- `Zicht\Bundle\MenuBundle\Admin\Extension\TreeAdminExtension` to allow language and name fields.

## 4.0.0 - 2020-05-15
### Added
- Support for Symfony 4.x
### Removed
- Support for Symfony 3.x
### Changed
- Removed Zicht(Test)/Bundle/MenuBundle/ directory depth: moved all code up directly into src/ and test/

## 3.0.6 - 2021-03-21
### Fixed
- `ValidateNestedTreeProvider` now extends `StatusProviderHelper` so the `check` method can be utilized and this Provider
  no longer has its workload implemented in the `__construct` as previously fixed in `4.1.1`

## 3.0.5 - 2020-05-15
### Changed
- Switched from PSR-0 to PSR-4 autoloading

## 3.0.4 - 2019-06-17
### Fixed
- Fixed a bug that would never allow the menu item `name` property to be changed, as it was dependant on ACL checking of the menu item's `DELETE` attribute, but which was never implemented.

## 3.0.2 - 2018-08-14
### Fixed
- Prevent some case(s) of `get_class` to be called with `null`, this will give a warning in PHP 7.2

## 3.0.1 - 2018-07-12
### Changed
- Update encapsulation in MenuBuilder

## 3.0.0 - 2018-06-29
### Added
- Support for Symfony 3.x and Twig 2.x
### Removed
- Support for Symfony 2.x and Twig 1.x

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
