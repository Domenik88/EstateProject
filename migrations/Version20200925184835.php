<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925184835 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE listing ADD status VARCHAR(20) NOT NULL DEFAULT 'new'");
        $this->addSql("ALTER TABLE listing ADD processing_status VARCHAR(20) NOT NULL DEFAULT 'none'");
        $this->addSql('ALTER TABLE listing ADD last_update_from_feed TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listing DROP status');
        $this->addSql('ALTER TABLE listing DROP processing_status');
        $this->addSql('ALTER TABLE listing DROP last_update_from_feed');
    }
}
