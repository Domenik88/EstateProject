<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207215255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE viewing (id SERIAL NOT NULL, user_id INT NOT NULL, listing_id INT NOT NULL, status TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX viewing_user_idx ON viewing (user_id)');
        $this->addSql('CREATE UNIQUE INDEX viewing_listing_idx ON viewing (listing_id)');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT viewing_user_id_fk FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT viewing_listing_id_fk FOREIGN KEY (listing_id) REFERENCES listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD phone_number TEXT NOT NULL DEFAULT \'00000000000\'');
        $this->addSql('ALTER TABLE "user" ADD name TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE viewing');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP name');
    }
}
