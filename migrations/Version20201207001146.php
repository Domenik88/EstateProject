<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207001146 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE viewing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE viewing (id INT NOT NULL, user_id INT NOT NULL, listing_id INT NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5BB4698A76ED395 ON viewing (user_id)');
        $this->addSql('CREATE INDEX IDX_F5BB4698D4619D1A ON viewing (listing_id)');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT FK_F5BB4698A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT FK_F5BB4698D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(20) NOT NULL DEFAULT \'+00000000000\'');
        $this->addSql('ALTER TABLE "user" ADD name VARCHAR(40) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE viewing_id_seq CASCADE');
        $this->addSql('DROP TABLE viewing');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP name');
    }
}
