# Changelog

All notable changes to `nasimail/laravel-client` are documented in this file.

## [Unreleased]

### Added

- PHPUnit test suite scaffold and unit tests for `NasiMailClient` validation paths.
- GitHub Actions workflow at `.github/workflows/tests.yml` to run tests on push and pull requests.
- Composer dependency caching in CI for faster workflow runs.

## [1.0.0] - 2026-06-07

### Added

- Initial local package scaffold under `packages/nasimail`.
- Service provider with package auto-discovery.
- `NasiMailClient` with two drivers:
  - `api` driver for `POST /api/v1/messages`.
  - `mail` driver that uses Laravel Mail transport.
- Custom Laravel mail transport driver: `nasimail`.
- Package config: `config/nasimail-client.php`.
- Installation and usage guide in package README.
- App integration via Composer path repository.
- Minimal app mail config in `config/mail.php` with `nasimail` transport support.
