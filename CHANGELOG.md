# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.1] - Unreleased

### Added
- Schema documentation available at https://oxid-esales.github.io/graphql-storefront-module
- Classes:
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\BasketByTitleAndUserIdFilterList`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\Sorting`
- Method `OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository::basketExistsByTitleAndUserId()`

### Fixed
- Remove ``final`` statement from shop extending classes [PR-3](https://github.com/OXID-eSales/graphql-storefront-module/pull/3).

### Changed
- Use Rights annotation instead of Logged so that an anonymous token user could create/modify basket and place an order.

## [0.1.0] - 2020-12-16

- Initial release
- deprecates
    - `oxid-esales/graphql-catalogue`
    - `oxid-esales/graphql-account`
    - `oxid-esales/graphql-checkout`

[0.1.1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v0.1.0...master
[0.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/releases/tag/v0.1.0
