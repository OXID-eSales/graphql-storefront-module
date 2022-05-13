<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201006091606 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `oxvouchers`
          ADD COLUMN `OEGQL_BASKETID` char(32)
          character set latin1 collate latin1_general_ci NOT NULL DEFAULT ''
          COMMENT 'Relation to oxuserbasket';");
    }

    public function down(Schema $schema): void
    {
    }
}
