# Changelog

All notable changes to `nasimail/laravel-client` are documented in this file.

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
