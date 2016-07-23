<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160621000014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE campaignchain_location_linkedin (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, identifier VARCHAR(255) NOT NULL, displayName VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, firstName VARCHAR(255) DEFAULT NULL, lastName VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, profileUrl VARCHAR(255) DEFAULT NULL, profileImageUrl VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, websiteLink VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_626D65BE772E836A (identifier), INDEX IDX_626D65BE64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaignchain_location_linkedin ADD CONSTRAINT FK_626D65BE64D218E FOREIGN KEY (location_id) REFERENCES campaignchain_location (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE campaignchain_location_linkedin');
    }
}
