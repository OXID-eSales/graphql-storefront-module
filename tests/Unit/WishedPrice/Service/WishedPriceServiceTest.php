<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\WishedPrice\Service;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\Eshop\Application\Model\PriceAlarm as WishedPriceModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceOutOfBounds;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceFactoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceNotification;
use OxidEsales\GraphQL\Storefront\WishedPrice\Input\WishedPriceInputInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\RelationService;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPrice as WishedPriceService;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPriceInputValidator;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPriceInputValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(WishedPriceService::class)]
final class WishedPriceServiceTest extends TestCase
{
    #[Test]
    public function setThrowsProductNotFoundWhenProductMissingBeforeAnySideEffects(): void
    {
        $productId = uniqid();
        $price = 12.5;
        $input = $this->getInputStub(productId: $productId, currencyName: 'EUR', price: $price);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($productId, Product::class)
            ->willThrowException(new NotFound());
        $repositoryMock->expects($this->never())->method('saveModel');

        $authenticationMock = $this->createMock(Authentication::class);
        $authenticationMock->expects($this->never())->method('getUser');

        $validatorMock = $this->createMock(WishedPriceInputValidatorInterface::class);
        $validatorMock->expects($this->once())->method('validatePrice')->with($price);

        $factoryMock = $this->createMock(WishedPriceFactoryInterface::class);
        $factoryMock->expects($this->never())->method('createWishedPrice');

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationMock,
            validator: $validatorMock,
            factory: $factoryMock,
        );

        $this->expectException(ProductNotFound::class);

        $sut->set($input);
    }

    #[Test]
    public function setThrowsProductNotFoundWhenPriceAlarmDisabledBeforeAnySideEffects(): void
    {
        $productId = uniqid();
        $input = $this->getInputStub(productId: $productId, currencyName: 'EUR', price: 12.5);

        $product = new Product(
            $this->createConfiguredStub(EshopProductModel::class, ['isPriceAlarm' => false])
        );

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($productId, Product::class)
            ->willReturn($product);
        $repositoryMock->expects($this->never())->method('saveModel');

        $authenticationMock = $this->createMock(Authentication::class);
        $authenticationMock->expects($this->never())->method('getUser');

        $factoryMock = $this->createMock(WishedPriceFactoryInterface::class);
        $factoryMock->expects($this->never())->method('createWishedPrice');

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationMock,
            factory: $factoryMock,
        );

        $this->expectException(ProductNotFound::class);

        $sut->set($input);
    }

    #[Test]
    public function setPersistsWishedPriceAndReturnsReloadedDataType(): void
    {
        $userId = uniqid();
        $userEmail = uniqid() . '@oxid-esales.com';
        $productId = uniqid();
        $wishedPriceId = uniqid();
        $currencyName = 'EUR';
        $price = 99.9;

        $product = new Product(
            $this->createConfiguredStub(EshopProductModel::class, ['isPriceAlarm' => true])
        );

        $createdWishedPrice = new WishedPriceDataType(
            $this->createConfiguredStub(WishedPriceModel::class, [
                'getId' => $wishedPriceId,
                'getRawFieldData' => $userEmail,
            ])
        );

        $reloadedWishedPrice = new WishedPriceDataType($this->createStub(WishedPriceModel::class));

        $factoryMock = $this->createMock(WishedPriceFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createWishedPrice')
            ->with($userId, $userEmail, new ID($productId), $currencyName, $price)
            ->willReturn($createdWishedPrice);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->willReturnCallback(
                function (string $id, string $type) use ($product, $reloadedWishedPrice) {
                    if ($type === Product::class) {
                        return $product;
                    }

                    return $reloadedWishedPrice;
                }
            );
        $repositoryMock->expects($this->once())->method('saveModel');

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn(
            new User(
                $this->createConfiguredStub(EshopUserModel::class, [
                    'getId' => $userId,
                    'getRawFieldData' => $userEmail,
                ])
            )
        );

        $validatorMock = $this->createMock(WishedPriceInputValidatorInterface::class);
        $validatorMock->expects($this->once())->method('validatePrice')->with($price);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationStub,
            validator: $validatorMock,
            factory: $factoryMock,
        );

        $result = $sut->set(
            $this->getInputStub(productId: $productId, currencyName: $currencyName, price: $price)
        );

        $this->assertSame($reloadedWishedPrice, $result);
    }

    #[Test]
    public function setPropagatesRealValidatorWishedPriceOutOfBounds(): void
    {
        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->never())->method('getById');
        $repositoryMock->expects($this->never())->method('saveModel');

        $authenticationMock = $this->createMock(Authentication::class);
        $authenticationMock->expects($this->never())->method('getUser');

        $factoryMock = $this->createMock(WishedPriceFactoryInterface::class);
        $factoryMock->expects($this->never())->method('createWishedPrice');

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationMock,
            validator: new WishedPriceInputValidator(),
            factory: $factoryMock,
        );

        $this->expectException(WishedPriceOutOfBounds::class);

        $sut->set($this->getInputStub(productId: uniqid(), currencyName: 'EUR', price: 0.0));
    }

    private function getInputStub(string $productId, string $currencyName, float $price): WishedPriceInputInterface
    {
        $input = $this->createStub(WishedPriceInputInterface::class);
        $input->method('getProductId')->willReturn(new ID($productId));
        $input->method('getCurrencyName')->willReturn($currencyName);
        $input->method('getPrice')->willReturn($price);

        return $input;
    }

    private function getSut(
        ?RepositoryInterface $repository = null,
        ?Authentication $authentication = null,
        ?WishedPriceInputValidatorInterface $validator = null,
        ?WishedPriceFactoryInterface $factory = null,
    ): WishedPriceService {
        return new WishedPriceService(
            $repository ?? $this->createStub(RepositoryInterface::class),
            $authentication ?? $this->createStub(Authentication::class),
            $this->createStub(Authorization::class),
            (new ReflectionClass(RelationService::class))->newInstanceWithoutConstructor(),
            $this->getNotification(),
            $validator ?? $this->createStub(WishedPriceInputValidatorInterface::class),
            $factory ?? $this->createStub(WishedPriceFactoryInterface::class),
        );
    }

    private function getNotification(): WishedPriceNotification
    {
        $emailStub = $this->createStub(Email::class);
        $emailStub->method('sendPriceAlarmNotification')->willReturn(true);

        $oxNewFactoryStub = $this->createStub(OxNewFactoryInterface::class);
        $oxNewFactoryStub->method('getModel')->willReturn($emailStub);

        return new WishedPriceNotification($oxNewFactoryStub);
    }
}
