# Changelog

All notable changes to `laravel-envbar` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

## [0.3.0] - 2026-06-01

### Added 
- add preview image to README
- add custom data provider functionality to allow users to define their own custom info segments with arbitrary data (e.g. from config or via callback)

## [0.2.0] - 2026-04-15

### Added
- add `envbar:status` Artisan command to display current status and metadata in console
- add `envbar:check` Artisan command to check if Envbar can be displayed

### Fixed
- fix incorrect environment retrieval in certain edge cases (e.g. when running in a non-git environment or when certain server variables are missing)
- fix potential issues with bar injection in non-standard HTML responses (e.g. when `<body>` tag is missing or when response content is not HTML)
- fix potential issues with favicon overlay when original favicon is not found or when canvas is not supported 

## [0.1.2] - 2026-04-14

### Fixed
- fix incorrect body margin when bar is visible (32px -> 24px)
- fix service provider boot method to ensure middleware is registered after all providers are loaded

## [0.1.1] - 2026-04-14

### Added
- add version in composer.json

## [0.1.0] - 2026-04-14

### Fixed 
- fix git branch/commit retrieval in non-git environments

### Added
- add support for Livewire requests (X-Livewire header)

## [0.0.1] - 2026-04-14

### Added
- Visual environment indicator bar with inline CSS/JS (zero external dependencies)
- Auto-injection into HTML responses via `InjectEnvbar` middleware
- Per-environment configuration: label, background color, text color, and icon
- Configurable info segments: app name, PHP version, Laravel version, git branch, git commit SHA, hostname, database name, authenticated user, server timestamp
- Collapsible bar with state persistence in `localStorage`
- Minimized pill indicator when bar is collapsed
- Environment switcher with quick links to other configured environments
- Favicon overlay with colored badge via canvas
- Top/bottom positioning with automatic body margin offset
- Laravel Gate authorization support
- Inter variable font (Google Fonts) for consistent typography
- Publishable config (`envbar-config`) and views (`envbar-views`)
- Auto-registration via Laravel package discovery
- Support for Laravel 10, 11, 12, and 13
- Feature tests for bar injection, production exclusion, and environment label rendering 