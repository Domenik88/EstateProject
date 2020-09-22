<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921184614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE listing (id SERIAL NOT NULL, mls_num VARCHAR(20) NOT NULL, feed_listing_id VARCHAR(20) NOT NULL, feed_id VARCHAR(255) NOT NULL, list_price DOUBLE PRECISION DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, photos_count INT DEFAULT NULL, unparsed_address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX listing_mls_num_feed_listing_id_idx ON listing (mls_num, feed_listing_id)');
        $this->addSql('CREATE TABLE feed (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, busy BOOLEAN NOT NULL, last_run_time DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql("INSERT INTO feed (name,busy) VALUES ('ddf',false)");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE feed');
        $this->addSql('DROP TABLE listing');
    }
}
