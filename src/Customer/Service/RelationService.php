<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Service\InvoiceAddress as InvoiceAddressService;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Customer as CustomerInfrastructure;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository as CustomerRepository;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\NewsletterStatusForUserNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\NewsletterStatus as NewsletterStatusService;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderFile;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Review\DataType\ReviewFilterList;
use OxidEsales\GraphQL\Storefront\Review\Service\Review as ReviewService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @ExtendType(class=CustomerDataType::class)
 */
final class RelationService
{
    /** @var ReviewService */
    private $reviewService;

    /** @var NewsletterStatusService */
    private $newsletterStatusService;

    /** @var CustomerRepository */
    private $customerRepository;

    /** @var InvoiceAddressService */
    private $invoiceAddressService;

    /** @var BasketService */
    private $basketService;

    /** @var CustomerInfrastructure */
    private $customerInfrastructure;

    public function __construct(
        ReviewService $reviewService,
        NewsletterStatusService $newsletterStatusService,
        CustomerRepository $customerRepository,
        InvoiceAddressService $invoiceAddressService,
        BasketService $basketService,
        CustomerInfrastructure $customerInfrastructure
    ) {
        $this->reviewService = $reviewService;
        $this->newsletterStatusService = $newsletterStatusService;
        $this->customerRepository = $customerRepository;
        $this->invoiceAddressService = $invoiceAddressService;
        $this->basketService = $basketService;
        $this->customerInfrastructure = $customerInfrastructure;
    }

    /**
     * @Field()
     *
     * @return ReviewDataType[]
     */
    public function getReviews(CustomerDataType $customer): array
    {
        return $this->reviewService->reviews(
            new ReviewFilterList(
                new IDFilter(
                    new ID(
                        (string)$customer->getId()
                    )
                )
            )
        );
    }

    /**
     * @Field()
     */
    public function getNewsletterStatus(): ?NewsletterStatusType
    {
        try {
            return $this->newsletterStatusService->newsletterStatus();
        } catch (NewsletterStatusForUserNotFound $e) {
            return null;
        }
    }

    /**
     * @Field()
     *
     * @return DeliveryAddress[]
     */
    public function deliveryAddresses(CustomerDataType $customer): array
    {
        return $this->customerRepository->addresses($customer);
    }

    /**
     * @Field()
     */
    public function invoiceAddress(): InvoiceAddressDataType
    {
        return $this->invoiceAddressService->customerInvoiceAddress();
    }

    /**
     * @Field()
     */
    public function getBasket(CustomerDataType $customer, string $title): BasketDataType
    {
        return $this->basketService->basketByOwnerAndTitle($customer, $title);
    }

    /**
     * @Field()
     *
     * @return BasketDataType[]
     */
    public function getBaskets(CustomerDataType $customer): array
    {
        return $this->basketService->basketsByOwner($customer);
    }

    /**
     * @Field()
     *
     * @return OrderDataType[]
     */
    public function getOrders(CustomerDataType $customer, ?PaginationFilter $pagination = null): array
    {
        return $this->customerInfrastructure->getOrders($customer, $pagination);
    }

    /**
     * @Field
     *
     * @return OrderFile[]
     */
    public function getFiles(CustomerDataType $customer): array
    {
        return $this->customerInfrastructure->getOrderFiles($customer);
    }
}
