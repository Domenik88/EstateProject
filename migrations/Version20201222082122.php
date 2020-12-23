<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201222082122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX listing_feed_id_feed_listing_id_idx');
        $this->addSql('CREATE UNIQUE INDEX listing_feed_id_feed_listing_id_idx ON listing (feed_id, feed_listing_id) WHERE (deleted_date IS NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX listing_feed_id_feed_listing_id_idx');
        $this->addSql('CREATE UNIQUE INDEX listing_feed_id_feed_listing_id_idx ON listing (feed_id, feed_listing_id)');
    }
}
