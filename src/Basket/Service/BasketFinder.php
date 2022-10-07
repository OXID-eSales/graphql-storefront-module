<?php

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket as PublicBasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedInfrastructure;
use TheCodingMachine\GraphQLite\Types\ID;

final class BasketFinder
{
    private BasketInfrastructure $basketInfrastructure;
    private SharedInfrastructure $sharedInfrastructure;
    private BasketRepository $basketRepository;
    private Authentication $authenticationService;

    public function __construct(
        BasketInfrastructure $basketInfrastructure,
        SharedInfrastructure $sharedInfrastructure,
        BasketRepository $basketRepository,
        Authentication $authenticationService,
    ) {
        $this->basketInfrastructure = $basketInfrastructure;
        $this->sharedInfrastructure = $sharedInfrastructure;
        $this->basketRepository = $basketRepository;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @throws BasketAccessForbidden
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function basket(ID $id): BasketDataType
    {
        $basket = $this->getAuthenticatedCustomerBasket($id);

        $this->basketInfrastructure->checkBasketItems($basket->getEshopModel());

        $this->sharedInfrastructure->getBasket($basket);

        return $basket;
    }

    public function publicBasket(ID $id): PublicBasketDataType
    {
        $basket = $this->basketRepository->getBasketById((string)$id);

        if ($basket->public() === false || $basket->title() === 'noticelist') {
            throw BasketAccessForbidden::basketIsPrivate();
        }

        return new PublicBasketDataType($basket->getEshopModel());
    }

    /**
     * @throws BasketAccessForbidden
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function getAuthenticatedCustomerBasket(ID $id): BasketDataType
    {
        $basket = $this->basketRepository->getBasketById((string)$id);
        $userId = (string)$this->authenticationService->getUser()->id();

        if (!$basket->belongsToUser($userId)) {
            throw BasketAccessForbidden::byAuthenticatedUser();
        }

        return $basket;
    }

    public function basketByOwnerAndTitle(CustomerDataType $customer, string $title): BasketDataType
    {
        return $this->basketRepository->customerBasketByTitle($customer, $title);
    }

    /**
     * @return BasketDataType[]
     */
    public function basketsByOwner(CustomerDataType $customer): array
    {
        return $this->basketRepository->customerBaskets($customer);
    }


}
