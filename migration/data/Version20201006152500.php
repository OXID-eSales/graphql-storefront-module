<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201006152500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `oxuserbaskets`
            ADD COLUMN `OEGQL_PAYMENTID` char(32)
                character set latin1 collate latin1_general_ci DEFAULT NULL
                COMMENT 'Relation to oxpayments.oxid',
            ADD COLUMN `OEGQL_DELADDRESSID` char(32)
                character set latin1 collate latin1_general_ci DEFAULT NULL
                COMMENT 'Relation to oxaddress.oxid, if empty the invoice address from oxuser table is used',
            ADD COLUMN `OEGQL_DELIVERYMETHODID` char(32)
                character set latin1 collate latin1_general_ci DEFAULT NULL
                COMMENT 'Relation to oxdeliveryset.oxid';");
    }

    public function down(Schema $schema): void
    {
    }
}
