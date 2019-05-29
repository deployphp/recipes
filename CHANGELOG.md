# Changelog


## master
[6.2.0...master](https://github.com/deployphp/recipes/compare/6.2.0...master)

### Added
- Add APCu support in cachetool recipe
- Fixed Sentry deployment recipe, updated to work with latest deployer/deployer

## 6.2.0
[6.1.0...6.2.0](https://github.com/deployphp/recipes/compare/6.1.0...6.2.0)

### Fixed
- Fixed malformed New Relic deployment payload when ID/key falsy (#193)
- Fixed Sentry deployment recipe, updated to support latest API

### Added
- Added composer dev dependency to include deployer/deployer
- Added info about commits to sentry deployments
- Added cimonitor recipe, based on slack recipe

## 6.1.0
[6.0.2...6.1.0](https://github.com/deployphp/recipes/compare/6.0.2...6.1.0)

### Added
- Added `cachetool_args` option.
- Added grafana recipe.
- Added yammer recipe, based on Slack recipe.
- Added Google Hangouts Chat recipe, based on Slack recipe.
- Added support for New Relic deployment revision as config.

### Fixed
- Fixed telegram recipe (#170) and updated docs
- Fixed newrelic recipe
- Fixed discord recipe (#181) and updated docs
- Fixed sending complex messages to discord

## 6.0.2
[6.0.1...6.0.2](https://github.com/deployphp/recipes/compare/6.0.1...6.0.2)

### Added
- Added raygun recipe.
- Added telegram notifier, based on Slack recipe.
- Added slack:notify:failure task
- Added ability to change hipchat server URL for self hosted instances

### Fixed
- Fixed bug with message formatting in telegram notifications
- Execute rsync with ssh options.

## 6.0.1
[6.0.0...6.0.1](https://github.com/deployphp/recipes/compare/6.0.0...6.0.1)

### Fixed
- Fixed bug with test func in rsync recipe
