<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125105530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, name VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT fk_232b318cb03a8386');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT fk_232b318c896dbbde');
        $this->addSql('DROP INDEX uniq_232b318c989d9b62');
        $this->addSql('DROP INDEX idx_232b318c896dbbde');
        $this->addSql('DROP INDEX idx_232b318cb03a8386');
        $this->addSql('ALTER TABLE game DROP created_by_id');
        $this->addSql('ALTER TABLE game DROP updated_by_id');
        $this->addSql('ALTER TABLE game DROP slug');
        $this->addSql('ALTER TABLE game DROP created_at');
        $this->addSql('ALTER TABLE game DROP updated_at');
        $this->addSql('ALTER TABLE game DROP image_size');
        $this->addSql('ALTER TABLE game RENAME COLUMN image_name TO logo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP TABLE team');
        $this->addSql('ALTER TABLE game ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE game ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE game ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE game ADD image_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game RENAME COLUMN logo TO image_name');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT fk_232b318cb03a8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT fk_232b318c896dbbde FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_232b318c989d9b62 ON game (slug)');
        $this->addSql('CREATE INDEX idx_232b318c896dbbde ON game (updated_by_id)');
        $this->addSql('CREATE INDEX idx_232b318cb03a8386 ON game (created_by_id)');
    }
}
