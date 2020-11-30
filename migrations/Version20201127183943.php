<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201127183943 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listing ADD type VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ADD ownership_type VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ADD bedrooms INT DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ADD living_area INT DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ADD lot_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ADD year_built INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE listing DROP type');
        $this->addSql('ALTER TABLE listing DROP ownership_type');
        $this->addSql('ALTER TABLE listing DROP bedrooms');
        $this->addSql('ALTER TABLE listing DROP living_area');
        $this->addSql('ALTER TABLE listing DROP lot_size');
        $this->addSql('ALTER TABLE listing DROP year_built');
    }
}
