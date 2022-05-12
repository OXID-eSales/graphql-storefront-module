<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Basket;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Application\Model\UserBasket as EshopUserBasketModel;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as BasketInfrastructure;

final class ShopBasketTest extends TestCase
{
    private const PERSONALIZABLE_PRODUCT_ID = 'f4f73033cf5045525644042325355732';

    private const TEST_USER_ID = '245ad3b5380202966df6ff128e9eecaq';

    public function testCreateBasketModelFromUserBasket(): void
    {
        $user = oxNew(EshopUserModel::class);
        $user->load(self::TEST_USER_ID);

        //create EshopBasketModel
        $basketModel = oxNew(TestEshopBasketModel::class);
        $basketModel->setUser($user);
        $basketModel->addToBasket(self::PERSONALIZABLE_PRODUCT_ID, 1, null, ['details' => 'first']);
        $basketModel->addToBasket(self::PERSONALIZABLE_PRODUCT_ID, 2, null, ['details' => 'second']);
        $basketModel->addToBasket(self::PERSONALIZABLE_PRODUCT_ID, 3, null, ['details' => 'third']);
        $basketModel->calculateBasket(true);
        $userBasketId = $basketModel->saveAsUserBasket('mycart');

        $original = $basketModel->getContents();
        asort($original);
        $this->assertEquals(3, count($basketModel->getContents()));
        $originalHash = $this->getBasketHash($basketModel->getContents());

        $userBasketModel = oxNew(EshopUserBasketModel::class);
        $userBasketModel->load($userBasketId);
        $basketDataType = new BasketDataType($userBasketModel);

        $basketInfrastructure = ContainerFactory::getInstance()->getContainer()->get(BasketInfrastructure::class);
        $reconstructedBasketModel = $basketInfrastructure->getBasket($basketDataType);
        $reconstructed = $reconstructedBasketModel->getContents();

        asort($reconstructed);
        $this->assertEquals(3, count($reconstructed));
        $this->assertSame($originalHash, $this->getBasketHash($reconstructed));
    }

    private function getBasketHash(array $items): string
    {
        $toHash = '';

        foreach ($items as $key => $basketItem) {
            $toHash .= $key;
            $toHash .= $basketItem->getAmount();
            $toHash .= $basketItem->getArticle()->getId();
        }

        return md5($toHash);
    }
}

final class TestEshopBasketModel extends EshopBasketModel // phpcs:ignore
{
    public function saveAsUserBasket(string $title): string
    {
        $this->enableSaveToDataBase();
        $this->_save();

        $userBasketModel = $this->getUser()->getBasket('savedbasket');
        $userBasketModel->assign(
            [
                'oxtitle' => $title,
            ]
        );
        $userBasketModel->save();

        return $userBasketModel->getId();
    }
}
