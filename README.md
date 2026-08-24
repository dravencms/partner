# DravenCMS Partner

Partner/logo administration and frontend presentation for DravenCMS. Partner records combine descriptive content, managed files, ordering, activation, and tags.

## Features

- Partner administration with file and tag integration.
- Ordered and active partner records.
- Overview, main, and bar frontend components.
- CMS component integration through DravenCMS Structure.
- Admin menu and ACL fixtures.

## Installation

```bash
composer require dravencms/partner
```

The package loader registers the extension, admin presenter, component factories, repositories, fixtures, and Doctrine mappings. Apply the package schema and load fixtures through the application's normal workflow.

## Frontend Integration

With `dravencms/structure` installed, the package exposes three CMS components:

- Partner Overview
- Partner Main
- Partner Bar

The overview and bar factories require an `ICmsActionOption`; the main factory also supports creation without one. Applications can inject these factories directly when rendering partners outside the CMS page builder.

Partner images are managed through `dravencms/file`, and classifications use `dravencms/tag`. Preserve those relationships when importing or deleting records so file cleanup and tag queries remain accurate.

## License

This package is licensed under the LGPL-3.0 license.
