<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

class BasketBaseCest extends BaseCest
{
    protected function basketCreateMutation(
        AcceptanceTester $I,
        string $title
    ): array {
        $mutation = '
            mutation ($title: String!) {
                basketCreate(basket: {title: $title}) {
                    id
                }
            }
        ';

        $variables = [
            'title' => $title,
        ];

        $I->sendGQLQuery($mutation, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray()['data']['basketCreate'];
    }

    protected function basketQuery(
        AcceptanceTester $I,
        string $basketId
    ): array {
        $query = '
            query ($basketId: ID!) {
                basket(basketId: $basketId) {
                    id
                    items {
                        id
                        amount
                        product {
                            id
                        }
                    }
                }
            }
        ';

        $variables = [
            'basketId' => $basketId,
        ];

        $I->sendGQLQuery($query, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray()['data']['basket'];
    }

    protected function basketAddItemMutation(
        AcceptanceTester $I,
        string $basketId,
        string $productId,
        int $amount = 1
    ): array {
        $mutation = '
            mutation ($basketId: ID!, $productId: ID!, $amount: Float!) {
                basketAddItem(
                    basketId: $basketId,
                    productId: $productId,
                    amount: $amount
                ) {
                    id
                    items {
                        product {
                            id
                        }
                        amount
                    }
                    lastUpdateDate
                }
            }
        ';

        $variables = [
            'basketId'  => $basketId,
            'productId' => $productId,
            'amount'    => $amount,
        ];

        $I->sendGQLQuery($mutation, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    protected function basketRemoveItemMutation(
        AcceptanceTester $I,
        string $basketId,
        string $basketItemId,
        int $amount = 1
    ): array {
        $mutation = '
            mutation ($basketId: ID!, $basketItemId: ID!, $amount: Float!) {
                basketRemoveItem(
                    basketId: $basketId,
                    basketItemId: $basketItemId,
                    amount: $amount
                ) {
                    id
                }
            }
        ';

        $variables = [
            'basketId'     => $basketId,
            'basketItemId' => $basketItemId,
            'amount'       => $amount,
        ];

        $I->sendGQLQuery($mutation, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray()['data']['basketRemoveItem'];
    }
}
