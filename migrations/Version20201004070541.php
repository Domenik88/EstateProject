<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201004070541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE listing_master (id SERIAL NOT NULL, feed_id VARCHAR(20) NOT NULL, feed_listing_id VARCHAR(20) NOT NULL, updated_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX listing_master_feed_id_feed_listing_id_idx ON listing_master (feed_id, feed_listing_id)');
        $this->addSql('ALTER TABLE listing ALTER raw_data TYPE JSONB');
        $this->addSql('ALTER TABLE listing ALTER images_data TYPE JSONB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE listing_master');
        $this->addSql('ALTER TABLE listing ALTER raw_data TYPE JSON');
        $this->addSql('ALTER TABLE listing ALTER images_data TYPE JSON');
    }
}
