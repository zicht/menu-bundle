# 2.6.0
- Add a possibility to hint names of menus to be preloaded in the `zicht_menu` config. 
  This reduces the number of queries to a maximum of 3 for all menus that are common to a lot of pages.

# 2.5.0 #

- Implemented new UriVoter to fix BC break issue in KnpMenuBundle 2.x where voters are used to determine active state. This is a backwards compatible fix whereas the voter is only used when 2.x of the KnpBundle is used.

# 2.3 #

- adds a command to rewrite public urls in the menu to internal urls ("unalias" them)

