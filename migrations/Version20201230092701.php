<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201230092701 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page (id SERIAL NOT NULL, title TEXT NOT NULL, content TEXT DEFAULT NULL, status BOOLEAN NOT NULL, slug TEXT NOT NULL, type TEXT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX page_slug_idx ON page (slug)');
        $this->addSql('CREATE TABLE favorite_listings (user_id INT NOT NULL, listing_id INT NOT NULL, PRIMARY KEY(user_id, listing_id))');
        $this->addSql('CREATE INDEX IDX_302CC17FA76ED395 ON favorite_listings (user_id)');
        $this->addSql('CREATE INDEX IDX_302CC17FD4619D1A ON favorite_listings (listing_id)');
        $this->addSql('ALTER TABLE favorite_listings ADD CONSTRAINT FK_302CC17FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_listings ADD CONSTRAINT FK_302CC17FD4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE favorite_listings');
    }
}
