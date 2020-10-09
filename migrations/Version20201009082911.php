<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009082911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX admin_username_idx ON admin (username)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX user_email_idx ON "user" (email)');
        $this->addSql('INSERT INTO admin(username,roles,password) VALUES (\'admin\',\'["ROLE_ADMIN"]\',\'$argon2id$v=19$m=65536,t=4,p=1$KcGp6KPgoDShLTJ1a7QBpQ$O0XON1q12+l6se2hdtAZGgtv7Q8PfW2wGwk1ht5XoBI\')');
        $this->addSql('INSERT INTO "user"(email,roles,password) VALUES (\'user@example.com\',\'["ROLE_USER"]\',\'$argon2id$v=19$m=65536,t=4,p=1$KcGp6KPgoDShLTJ1a7QBpQ$O0XON1q12+l6se2hdtAZGgtv7Q8PfW2wGwk1ht5XoBI\')');
        $this->addSql('COMMENT ON COLUMN listing.images_data IS \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('COMMENT ON COLUMN listing.images_data IS NULL');
    }
}
