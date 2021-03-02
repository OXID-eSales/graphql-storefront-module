<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Contact;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group contact
 */
final class ContactCest extends BaseCest
{
    public function testContactRequest(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation{contactRequest(request:{
            email:"sometest@somedomain.com"
            firstName:"myName"
            lastName:"mySurname"
            salutation:"mr"
            subject:"some subject"
            message:"some message"
        })}');

        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['contactRequest']);
    }

    public function testContactRequestUsesShopValidation(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation{contactRequest(request:{
            email:"wrongEmail"
        })}');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame('ERROR_MESSAGE_INPUT_NOVALIDEMAIL', $result['errors'][0]['message']);
    }
}
