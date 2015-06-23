# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.5.2 - TBD

### Added

- [#8](https://github.com/zendframework/zend-diactoros/pull/8) adds a "strict"
  configuration option; when enabled (the default), the length of the address is
  checked to ensure it follows the specification.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#8](https://github.com/zendframework/zend-diactoros/pull/8) fixes bad
  behavior on the part of the `idn_to_utf8()` function, returning the original
  address in the case that the function fails.
