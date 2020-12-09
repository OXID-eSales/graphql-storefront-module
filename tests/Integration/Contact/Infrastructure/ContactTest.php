<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Contact\Infrastructure;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormBridgeInterface;
use OxidEsales\GraphQL\Base\Service\Legacy;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use OxidEsales\GraphQL\Storefront\Contact\DataType\ContactRequest;
use OxidEsales\GraphQL\Storefront\Contact\Infrastructure\Contact;

final class ContactTest extends TestCase
{
    public function testSendContactMailIsCalled(): void
    {
        $container = $this->getShopContainer();

        $contactFormBridge = $container->get(ContactFormBridgeInterface::class);

        /** @var Email $mailer */
        $mailer = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendContactMail'])
            ->getMock();
        $mailer->expects($this->once())
            ->method('sendContactMail')
            ->with(
                'someemail',
                'somesubject',
                $this->stringContains('somesalutation somefirstname somelastname (someemail)<br /><br />somemessage')
            )
            ->willReturn(true);

        /** @var Legacy $legacyServiceMock */
        $legacyServiceMock = $this
            ->getMockBuilder(Legacy::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEmail'])
            ->getMock();
        $legacyServiceMock->expects($this->any())
            ->method('getEmail')
            ->willReturn($mailer);

        $contactInfrastructure = new Contact(
            $legacyServiceMock,
            $contactFormBridge
        );

        $contactRequest = new ContactRequest(
            'someemail',
            'somefirstname',
            'somelastname',
            'somesalutation',
            'somesubject',
            'somemessage'
        );

        $this->assertTrue($contactInfrastructure->sendRequest($contactRequest));
    }

    private function getShopContainer()
    {
        $factory = ContainerFactory::getInstance();

        return $factory->getContainer();
    }
}
