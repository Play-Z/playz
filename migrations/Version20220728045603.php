<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728045603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT fk_bd5fb8d98afb5948');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT fk_bd5fb8d97c272830');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT fk_bd5fb8d9167ae8dd');
        $this->addSql('DROP INDEX idx_bd5fb8d98afb5948');
        $this->addSql('DROP INDEX idx_bd5fb8d97c272830');
        $this->addSql('DROP INDEX idx_bd5fb8d9167ae8dd');
        $this->addSql('ALTER TABLE tournament ADD price_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD price_first VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD price_second VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD price_third VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament DROP price_first_id');
        $this->addSql('ALTER TABLE tournament DROP price_second_id');
        $this->addSql('ALTER TABLE tournament DROP price_third_id');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9D614C7E7 ON tournament (price_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D9D614C7E7');
        $this->addSql('DROP INDEX IDX_BD5FB8D9D614C7E7');
        $this->addSql('ALTER TABLE tournament ADD price_second_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD price_third_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament DROP price_first');
        $this->addSql('ALTER TABLE tournament DROP price_second');
        $this->addSql('ALTER TABLE tournament DROP price_third');
        $this->addSql('ALTER TABLE tournament RENAME COLUMN price_id TO price_first_id');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT fk_bd5fb8d98afb5948 FOREIGN KEY (price_first_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT fk_bd5fb8d97c272830 FOREIGN KEY (price_second_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT fk_bd5fb8d9167ae8dd FOREIGN KEY (price_third_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_bd5fb8d98afb5948 ON tournament (price_first_id)');
        $this->addSql('CREATE INDEX idx_bd5fb8d97c272830 ON tournament (price_second_id)');
        $this->addSql('CREATE INDEX idx_bd5fb8d9167ae8dd ON tournament (price_third_id)');
    }
}
