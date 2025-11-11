# Laravel Print Template Package v1.0.1

## Overview

This is the second official release of the Laravel Print Template package. This package provides a complete CRUD management system for print templates in Laravel applications.

## Features

- Full CRUD operations for print templates
- JSON-based template configuration storage
- Automatic template code generation (DY+4-digit format)
- Standardized JSON responses using oh86/laravel-http-tools
- Soft delete support
- Resource association support
- Organization ID isolation (multi-tenancy)
- Automatic user and organization ID assignment from auth guard
- Creator and modifier relationship support with optional eager loading
- Support for array format HTTP methods in route configuration

## Installation

```bash
composer require xx19941215/laravel-print-template
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Xx19941215\PrintTemplate\PrintTemplateServiceProvider"
```

Run migrations:

```bash
php artisan migrate
```

## Requirements

- PHP ^7.4 || ^8.0
- Laravel Framework *
- oh86/laravel-http-tools ^1.1

## Changelog

### v1.0.1 (2025-11-11)

- feat(route): support array format for HTTP methods in route configuration

### v1.0.0 (2025-11-07)

- Initial release
- Complete CRUD API endpoints
- Template code generation (DY+4-digit format)
- Multi-tenancy support with org_id
- User relationship support (creator and modifier)
- Standardized JSON responses
- Configuration-driven user model path
- Soft delete support