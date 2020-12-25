<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224184453 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_listings (user_id INT NOT NULL, listing_id INT NOT NULL, PRIMARY KEY(user_id, listing_id))');
        $this->addSql('CREATE INDEX favorite_listings_user_id_idx ON favorite_listings (user_id)');
        $this->addSql('CREATE INDEX favorite_listings_listing_id_idx ON favorite_listings (listing_id)');
        $this->addSql('ALTER TABLE favorite_listings ADD CONSTRAINT favorite_listings_user_id_fk FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_listings ADD CONSTRAINT favorite_listings_listing_id_fk FOREIGN KEY (listing_id) REFERENCES listing (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE favorite_listings');
    }
}
