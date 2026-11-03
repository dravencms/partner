# DravenCMS Partner

Partner/logo administration and frontend presentation for DravenCMS. Partner records combine descriptive content, managed files, ordering, activation, and tags.

## Features

- Partner administration with file and tag integration.
- Ordered and active partner records.
- Overview, main, and bar frontend components.
- CMS component integration through DravenCMS Structure.
- Provider-defined admin navigation and ACL permissions.

## Installation

```bash
composer require dravencms/partner
```

The package loader registers the extension, admin presenter, component factories, repositories, and Doctrine mappings. Apply the package schema through the application's normal workflow; assign provider-defined permissions through the group editor.

## Frontend Integration

With `dravencms/structure` installed, the package exposes three CMS components:

- Partner Overview
- Partner Main
- Partner Bar

The overview and bar factories require an `ICmsActionOption`; the main factory also supports creation without one. Applications can inject these factories directly when rendering partners outside the CMS page builder.

Partner images are managed through `dravencms/file`, and classifications use `dravencms/tag`. Preserve those relationships when importing or deleting records so file cleanup and tag queries remain accurate.

## License

This package is licensed under the LGPL-3.0 license.

## Admin authorization and mutations

Administration uses User 3.1, Admin 3.2 and Application 0.3 guards. Screens and write callbacks enforce the existing provider-defined permissions server-side, including direct URLs and bulk callbacks. Mutating signals require POST and a session-bound action token; synchronize the matching admin assets when upgrading. See the User package's `user:security:migrate` instructions for the password-reset schema change.
