# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - Unreleased

### Removed
- Classes:
  - ``OxidEsales\GraphQL\Storefrnt\Address\Exception\DeliveryAddressMissingFields``
  - ``OxidEsales\GraphQL\Storefrnt\Address\Exception\InvoiceAddressMissingFields``

## [2.1.0] - Unreleased

### Added
New Event ``OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem``
New Exception ``OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields``

## [2.0.1] - 2022-01-03

### Added
New event``BeforeBasketRemoveOnPlaceOrder``

## [2.0.0] - 2021-12-08

### Added
- Support for PHP 8.0 and `oxid-esales/graphql-base ^6.0.0`
- ``OxidEsales\GraphQL\Storefront\Customer\Service::fetchCustomer`` is now public
- New Events:
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem``
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization``
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem``
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods``
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify``
  - ``OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemove``
  - ``BeforeBasketPayments`` updated with payment methods list so it can be adjusted
- Interface
  - `OxidEsales\GraphQL\Storefront\Basket\Event\BasketModifyInterface`
- Classes
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\AbstractBasket`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket`
  - `OxidEsales\GraphQL\Storefront\Basket\Service\PublicBasketRelationService`
  - `OxidEsales\GraphQL\Storefront\Basket\Exception\BasketItemAmountLimitedStock`
  - `OxidEsales\GraphQL\Storefront\Contact\Service\ContactInfrastructureAwareService`
  - `OxidEsales\GraphQL\Storefront\Shared\Exception\GraphQLServiceNotFound`
  - `OxidEsales\GraphQL\Storefront\Shared\Service\Authorization`
- Methods
  - `OxidEsales\GraphQL\Storefront\Basket\Controller\Basket::publicBasket()`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\Basket::getDeliveryAddressId()`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\Basket::getDeliveryMethodId()`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\Basket::getPaymentId()`
  - `OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem::basketId()`
  - `OxidEsales\GraphQL\Storefront\Basket\Exception\BasketItemNotFound::byIdInBasket()`
  - `OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder::productsNotOrdarable()`
  - `OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket::getBasketItemByProductId()`
  - `OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket::checkBasketItems()`

### Fixed
- Extracted basket authorization block to be handled with event so can be easier overwritten if needed
- Updated paths to bin directory in README [PR-5](https://github.com/OXID-eSales/graphql-storefront-module/pull/5)
- `OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\DeliveryMethod::getPosition()` method returns correct field data
- `OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPriceFilterList` now uses IDFilter instead of StringFilter

### Changed
- Method `OxidEsales\GraphQL\Storefront\Basket\Service\Basket::publicBasketsByOwnerNameOrEmail` now returns an array of `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket`
- `baskets(owner String)` now returns an array of `OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket` in order to not expose address or payment information on a public basket
- `deliveryAddressId` parameter of `basketSetDeliveryAddress` mutation can be null.
- `basketAddItem`, `basketRemoveItem`, `placeOrder` mutations and `basket` query honour stockflag and calls may give you back an error
- Drop support for PHP 7.3
- DataTypes related to `OxidEsales\Eshop\Core\Model\BaseModel` implement `OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface`
- `OxidEsales\Eshop\Core\Model\BaseModel::getRawFieldData()` is used instead of `OxidEsales\Eshop\Core\Model\BaseModel::getFieldData()`
- Improved basket product stock check and related error messages

### Removed
- Interface `OxidEsales\GraphQL\Storefront\Shared\DataType\DataType`
- Method `OxidEsales\GraphQL\Storefront\Customer\Service\Customer::basketOwner()`

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

[2.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.0.1...b-6.5.x
[2.0.1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v1.0.0-rc1...v1.0.0
[1.0.0-rc1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v0.1.0...v1.0.0-rc1
[0.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/releases/tag/v0.1.0
