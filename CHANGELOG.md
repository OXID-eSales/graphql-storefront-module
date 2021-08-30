# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## unreleased

### Changed

- `basketAddItem` mutation honours stockflag and calls may give you back an
  error
- Method `OxidEsales\GraphQL\Storefront\Basket\Service\Basket::publicBasketsByOwnerNameOrEmail` now returns an array of `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket`
- `baskets(owner String)` now returns an array of `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket` in order to not expose address or payment information on a public basket

### Added
- Classes
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket`
  - `OxidEsales\GraphQL\Storefront\Basket\Service\PublicBasketRelationService`

## [1.0.0] - 2021-05-28

### Changed

- `baskets(owner ID)` to `baskets(owner String)`

### Removed

- Query `foobar` left over from testing

## [1.0.0-rc1] - 2021-05-19

### Added
- Queries and Mutations for Storefront
- 3rd Party checkout support
- [Schema documentation](https://oxid-esales.github.io/graphql-storefront-module)

### Fixed
- Remove ``final`` statement from shop extending classes [PR-3](https://github.com/OXID-eSales/graphql-storefront-module/pull/3)
- Fixed compatibility issues related to `thecodingmachine/graphqlite:^4.1.2` update

### Changed
- Use `@Rights` annotation instead of `@Logged` so that an anonymus token user could create/modify basket and place an order

## [0.1.0] - 2020-12-16

- Initial release
- deprecates
    - `oxid-esales/graphql-catalogue`
    - `oxid-esales/graphql-account`
    - `oxid-esales/graphql-checkout`

[1.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v1.0.0-rc1...v1.0.0
[1.0.0-rc1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v0.1.0...v1.0.0-rc1
[0.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/releases/tag/v0.1.0
