<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Review\Service;

use OxidEsales\Eshop\Application\Model\Review as EshopReviewModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Review\Exception\RatingOutOfBounds;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewInputInvalid;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\Repository as ReviewRepository;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\ReviewFactoryInterface;
use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInputInterface;
use OxidEsales\GraphQL\Storefront\Review\Service\ActivityService;
use OxidEsales\GraphQL\Storefront\Review\Service\Review as ReviewService;
use OxidEsales\GraphQL\Storefront\Review\Service\ReviewInputValidator;
use OxidEsales\GraphQL\Storefront\Review\Service\ReviewInputValidatorInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReviewService::class)]
final class ReviewServiceTest extends TestCase
{
    #[Test]
    public function setThrowsProductNotFoundBeforeAnySideEffects(): void
    {
        $productId = uniqid();
        $input = $this->getInputStub(productId: $productId, text: 'great product', rating: 4);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($productId, Product::class)
            ->willThrowException(new NotFound());
        $repositoryMock->expects($this->never())->method('saveModel');

        $authenticationMock = $this->createMock(Authentication::class);
        $authenticationMock->expects($this->never())->method('getUser');

        $validatorMock = $this->createMock(ReviewInputValidatorInterface::class);
        $validatorMock->expects($this->once())->method('validateRating')->with(4);
        $validatorMock->expects($this->once())->method('validateReviewInput')->with($input);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationMock,
            validator: $validatorMock,
        );

        $this->expectException(ProductNotFound::class);

        $sut->set($input);
    }

    #[Test]
    public function setPersistsReviewAndReturnsReloadedDataType(): void
    {
        $userId = uniqid();
        $productId = uniqid();
        $reviewId = uniqid();
        $reloadedReview = new ReviewDataType($this->createStub(EshopReviewModel::class));

        $createdReview = new ReviewDataType(
            $this->createConfiguredStub(EshopReviewModel::class, [
                'getId' => $reviewId,
                'getRawFieldData' => '',
                'getReviewAndRatingListByUserId' => [],
            ])
        );

        $factoryMock = $this->createMock(ReviewFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createProductReview')
            ->with($userId, $productId, 'great product', '')
            ->willReturn($createdReview);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->willReturnCallback(
                function (string $id, string $type) use ($reloadedReview) {
                    if ($type === Product::class) {
                        return new \stdClass();
                    }

                    return $reloadedReview;
                }
            );
        $repositoryMock->expects($this->once())->method('saveModel');

        $authenticationStub = $this->createStub(Authentication::class);
        $authenticationStub->method('getUser')->willReturn(
            new User($this->createConfiguredStub(EshopUserModel::class, ['getId' => $userId]))
        );

        $validatorMock = $this->createMock(ReviewInputValidatorInterface::class);
        $validatorMock->expects($this->once())->method('validateRating')->with(null);
        $validatorMock->expects($this->once())->method('validateReviewInput');

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationStub,
            validator: $validatorMock,
            factory: $factoryMock,
        );

        $result = $sut->set(
            $this->getInputStub(productId: $productId, text: 'great product', rating: null)
        );

        $this->assertSame($reloadedReview, $result);
    }

    #[Test]
    public function setPropagatesRealValidatorRatingOutOfBounds(): void
    {
        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->never())->method('getById');
        $repositoryMock->expects($this->never())->method('saveModel');

        $factoryMock = $this->createMock(ReviewFactoryInterface::class);
        $factoryMock->expects($this->never())->method('createProductReview');

        $sut = $this->getSut(
            repository: $repositoryMock,
            validator: new ReviewInputValidator(),
            factory: $factoryMock,
        );

        $this->expectException(RatingOutOfBounds::class);

        $sut->set($this->getInputStub(productId: uniqid(), text: 'great product', rating: 6));
    }

    #[Test]
    public function setPropagatesRealValidatorReviewInputInvalid(): void
    {
        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->never())->method('getById');
        $repositoryMock->expects($this->never())->method('saveModel');

        $factoryMock = $this->createMock(ReviewFactoryInterface::class);
        $factoryMock->expects($this->never())->method('createProductReview');

        $sut = $this->getSut(
            repository: $repositoryMock,
            validator: new ReviewInputValidator(),
            factory: $factoryMock,
        );

        $this->expectException(ReviewInputInvalid::class);

        $sut->set($this->getInputStub(productId: uniqid(), text: null, rating: null));
    }

    private function getInputStub(string $productId, ?string $text, ?int $rating): ReviewInputInterface
    {
        $input = $this->createStub(ReviewInputInterface::class);
        $input->method('getProductId')->willReturn($productId);
        $input->method('getText')->willReturn($text);
        $input->method('getRating')->willReturn($rating);

        return $input;
    }

    private function getSut(
        ?RepositoryInterface $repository = null,
        ?Authentication $authentication = null,
        ?ReviewInputValidatorInterface $validator = null,
        ?ReviewFactoryInterface $factory = null,
    ): ReviewService {
        return new ReviewService(
            $repository ?? $this->createStub(RepositoryInterface::class),
            new ReviewRepository($this->createStub(QueryBuilderFactoryInterface::class)),
            $authentication ?? $this->createStub(Authentication::class),
            $this->createStub(Authorization::class),
            new ActivityService($this->createStub(Legacy::class)),
            $this->createStub(Legacy::class),
            $validator ?? $this->createStub(ReviewInputValidatorInterface::class),
            $factory ?? $this->createStub(ReviewFactoryInterface::class),
        );
    }
}
