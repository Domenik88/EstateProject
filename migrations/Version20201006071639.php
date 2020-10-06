<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201006071639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX listing_mls_num_feed_id_idx');
        $this->addSql('ALTER TABLE listing ADD state_or_province TEXT DEFAULT NULL');
        $this->addSql("CREATE UNIQUE INDEX listing_mls_num_feed_id_state_or_province_idx ON listing (mls_num, feed_id, state_or_province) WHERE state_or_province IS NOT NULL AND status = 'live' AND mls_num IS NOT NULL");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX listing_mls_num_feed_id_state_or_province_idx');
        $this->addSql('ALTER TABLE listing DROP state_or_province');
        $this->addSql('CREATE UNIQUE INDEX listing_mls_num_feed_id_idx ON listing (mls_num, feed_id)');
    }
}
