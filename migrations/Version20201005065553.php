<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005065553 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX listing_mls_num_feed_listing_id_idx');
        $this->addSql('ALTER TABLE listing ADD deleted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE listing ALTER mls_num DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX listing_mls_num_feed_id_idx ON listing (mls_num, feed_id)');
        $this->addSql('CREATE UNIQUE INDEX listing_feed_id_feed_listing_id_idx ON listing (feed_id, feed_listing_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX listing_mls_num_feed_id_idx');
        $this->addSql('DROP INDEX listing_feed_id_feed_listing_id_idx');
        $this->addSql('ALTER TABLE listing DROP deleted_date');
        $this->addSql('ALTER TABLE listing ALTER mls_num SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX listing_mls_num_feed_listing_id_idx ON listing (mls_num, feed_listing_id)');
    }
}
