# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.0] - Unreleased

### Added
- `variantSelections` query for fetching multidimensional variants [PR-11](https://github.com/OXID-eSales/graphql-storefront-module/pull/11)
- Add the `CategoryAttribute` data type and possibility to get Attributes for category [PR-13](https://github.com/OXID-eSales/graphql-storefront-module/pull/13)
- Workflow trigger to update schema in documentation
- Shop 7.1 dependencies match
- PHP 8.2 support
- Classes
    - `OxidEsales\GraphQL\Storefront\Manufacturer\DataType\ManufacturerImage`
    - `OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactory`
    - `OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface`
- Password forgotten feature
  - Mutations:
    - `customerPasswordForgotRequest`
    - `customerPasswordReset`
  - Classes:
    - `OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Password`
    - `OxidEsales\GraphQL\Storefront\Customer\Infrastructure\PasswordInterface`
    - `OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateHash`
    - `OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException`
    - `OxidEsales\GraphQL\Storefront\Customer\Infrastructure\RepositoryInterface`
    - `OxidEsales\GraphQL\Storefront\Customer\Service\CustomerInterface`
    - `OxidEsales\GraphQL\Storefront\Customer\Service\PasswordInterface`
    - `OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface`
  - Methods:
    - `OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository::saveNewPasswordForCustomer`
    - `OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository::getCustomerByPasswordUpdateHash`
    - `OxidEsales\GraphQL\Storefront\Customer\Service\Password::sendPasswordForgotEmail`
    - `OxidEsales\GraphQL\Storefront\Customer\Service\Password::resetPasswordByUpdateHash`
- Alias for `OxidEsales\GraphQL\Storefront\Customer\Service\Customer` for DI compatibility

### Changed
- Replace webmozart/path-util usage with symfony/filesystem
- New module logo
- Updated the structure to Codeception 5
- Modify github workflows to use new universal workflow
- Use new interfaces instead of direct classes

### Removed
- PHP 8.0 support
- Migration trigger on module activation
- `OxidEsales\GraphQL\Storefront\Shared\Service\Authorization`
- `byLength`in `OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch`

### Fixed
- Add error message to product response when variant loading is disabled [#0007421](https://bugs.oxid-esales.com/view.php?id=7421)

## [3.0.0] - 2023-06-08

### Added
- Support for PHP 8.1
- Support for MySQL 8
- Classes:
  - ``OxidEsales\GraphQL\Storefront\Basket\Service\BasketFinder``

### Removed
- Classes:
  - ``OxidEsales\GraphQL\Storefrnt\Address\Exception\DeliveryAddressMissingFields``
  - ``OxidEsales\GraphQL\Storefrnt\Address\Exception\InvoiceAddressMissingFields``
- Module upgraded for eshop version 7
    - NAME-constant removed from events
    - Support PHP 7.4

### Changed
- Refactored NotFound exception and children to create instance with constructor instead of static methods.
- Moved methods from Basket-Service to BasketItem, BasketVoucher and BasketFinder-Service
- License file was updated to be consistent with other OXID eSales modules
- Module upgraded for eshop version 7
    - Assetspath updated
    - Migrations config structure updated

## [2.2.0] - Unreleased

### Added
- Dependency on Base module. Base module cannot be deactivated till Storefront is active.
- Workflow trigger to update schema in documentation

### Changed
- License updated - now using OXID Module and Component License

## [2.1.0] - 2022-07-14

### Added
- New Event ``OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem``
- Not mandatory ``remark`` parameter added for ``placeOrder`` mutation [PR-9](https://github.com/OXID-eSales/graphql-storefront-module/pull/9)
- New service ``OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration``. Optionally supply core table name of list objects that must be instantiated with BaseModel::load().

### Fixed
- Code quality tools list simplified and reconfigured to fit our quality requirements
- Send registration email when creating a user
- Do not crush on not available Address country [PR-10](https://github.com/OXID-eSales/graphql-storefront-module/pull/10)

### Changed
- ``OxidEsales\GraphQL\Storefront\Basket\Service\BasketRelationService::owner()`` return value will be null for anonymous user.

## [2.0.2] - Unreleased

### Added
- Workflow trigger to update schema in documentation

## [2.0.1] - 2022-01-03

### Added
- New event``BeforeBasketRemoveOnPlaceOrder``

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

### Changed
- Use `@Rights` annotation instead of `@Logged` so that an anonymous token user could create/modify basket and place an order

## [0.1.0] - 2020-12-16

- Initial release
- deprecates
    - `oxid-esales/graphql-catalogue`
    - `oxid-esales/graphql-account`
    - `oxid-esales/graphql-checkout`

[3.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.1.0...v3.0.0
[2.2.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.1.0...b-6.5.x
[2.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.0.1...v2.1.0
[2.0.1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v1.0.0-rc1...v1.0.0
[1.0.0-rc1]: https://github.com/OXID-eSales/graphql-storefront-module/compare/v0.1.0...v1.0.0-rc1
[0.1.0]: https://github.com/OXID-eSales/graphql-storefront-module/releases/tag/v0.1.0
