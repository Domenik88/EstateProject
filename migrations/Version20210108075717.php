<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108075717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX viewing_user_idx');
        $this->addSql('DROP INDEX viewing_listing_idx');
        $this->addSql('CREATE INDEX viewing_user_id_idx ON viewing (user_id)');
        $this->addSql('CREATE INDEX viewing_listing_id_idx ON viewing (listing_id)');
        $this->addSql('CREATE UNIQUE INDEX viewing_user_id_listing_id_idx ON viewing (user_id, listing_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX viewing_user_id_idx');
        $this->addSql('DROP INDEX viewing_listing_id_idx');
        $this->addSql('DROP INDEX viewing_user_id_listing_id_idx');
        $this->addSql('CREATE UNIQUE INDEX viewing_user_idx ON viewing (user_id)');
        $this->addSql('CREATE UNIQUE INDEX viewing_listing_idx ON viewing (listing_id)');
    }
}
