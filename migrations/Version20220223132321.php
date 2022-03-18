<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223132321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD country VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD firstname VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD lastname VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD timezone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD organization VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD organization_position VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD newsletter BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP description');
        $this->addSql('ALTER TABLE "user" DROP country');
        $this->addSql('ALTER TABLE "user" DROP firstname');
        $this->addSql('ALTER TABLE "user" DROP lastname');
        $this->addSql('ALTER TABLE "user" DROP timezone');
        $this->addSql('ALTER TABLE "user" DROP organization');
        $this->addSql('ALTER TABLE "user" DROP organization_position');
        $this->addSql('ALTER TABLE "user" DROP newsletter');
    }
}
