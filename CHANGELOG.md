# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.6.0 - TBD

### Added

- [#18](https://github.com/zendframework/zend-validator/pull/18) adds a `GpsPoint`
  validator for validating GPS coordinates.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.2 - TBD

### Added

- [#8](https://github.com/zendframework/zend-validator/pull/8) adds a "strict"
  configuration option; when enabled (the default), the length of the address is
  checked to ensure it follows the specification.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#8](https://github.com/zendframework/zend-validator/pull/8) fixes bad
  behavior on the part of the `idn_to_utf8()` function, returning the original
  address in the case that the function fails.
- [#11](https://github.com/zendframework/zend-validator/pull/11) fixes
  `ValidatorChain::prependValidator()` so that it works on HHVM.
