<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220721223650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE announcement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE poule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE poule_equipe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE poule_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE price_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tournament_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tournament_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tournament_team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE announcement (id INT NOT NULL, team_announcement_id INT NOT NULL, tournament_announcement_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4DB9D91CB264F069 ON announcement (team_announcement_id)');
        $this->addSql('CREATE INDEX IDX_4DB9D91C79376C75 ON announcement (tournament_announcement_id)');
        $this->addSql('CREATE TABLE game (id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(128) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C989D9B62 ON game (slug)');
        $this->addSql('CREATE INDEX IDX_232B318CB03A8386 ON game (created_by_id)');
        $this->addSql('CREATE INDEX IDX_232B318C896DBBDE ON game (updated_by_id)');
        $this->addSql('CREATE TABLE game_type (id INT NOT NULL, game_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67CB3B05E48FD905 ON game_type (game_id)');
        $this->addSql('CREATE TABLE poule (id INT NOT NULL, tournament_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA1FEB4033D1A3E7 ON poule (tournament_id)');
        $this->addSql('CREATE TABLE poule_equipe (id INT NOT NULL, poule_id INT NOT NULL, tournament_team_id INT NOT NULL, nombre_victoire INT DEFAULT NULL, qualified BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EE99353E26596FD8 ON poule_equipe (poule_id)');
        $this->addSql('CREATE INDEX IDX_EE99353E426323A4 ON poule_equipe (tournament_team_id)');
        $this->addSql('CREATE TABLE poule_match (id INT NOT NULL, poule_id INT NOT NULL, equipe_une_id INT NOT NULL, equipe_deux_id INT NOT NULL, status BOOLEAN DEFAULT NULL, first_team_win BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_101664F426596FD8 ON poule_match (poule_id)');
        $this->addSql('CREATE INDEX IDX_101664F454E08946 ON poule_match (equipe_une_id)');
        $this->addSql('CREATE INDEX IDX_101664F46415CE33 ON poule_match (equipe_deux_id)');
        $this->addSql('CREATE TABLE price (id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, game_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(128) NOT NULL, description TEXT DEFAULT NULL, is_verified BOOLEAN NOT NULL, public BOOLEAN NOT NULL, emplacement INT NOT NULL, path VARCHAR(255) DEFAULT NULL, location VARCHAR(40) DEFAULT NULL, reddit_username VARCHAR(50) DEFAULT NULL, twitch_username VARCHAR(50) DEFAULT NULL, twitter_username VARCHAR(50) DEFAULT NULL, discord_server_token VARCHAR(20) DEFAULT NULL, youtube_username VARCHAR(50) DEFAULT NULL, elo INT DEFAULT NULL, nb_participation INT DEFAULT NULL, nb_win INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61F989D9B62 ON team (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61FB03A8386 ON team (created_by_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F896DBBDE ON team (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61FE48FD905 ON team (game_id)');
        $this->addSql('CREATE TABLE tournament (id INT NOT NULL, price_id INT DEFAULT NULL, game_id INT DEFAULT NULL, tournament_child_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, max_team_participant SMALLINT NOT NULL, max_team_players SMALLINT NOT NULL, status BOOLEAN DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, start_inscription_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, slug VARCHAR(128) NOT NULL, poule_type BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BD5FB8D9989D9B62 ON tournament (slug)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9D614C7E7 ON tournament (price_id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9E48FD905 ON tournament (game_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BD5FB8D93DDC24FE ON tournament (tournament_child_id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9B03A8386 ON tournament (created_by_id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9896DBBDE ON tournament (updated_by_id)');
        $this->addSql('CREATE TABLE tournament_tournament_team (tournament_id INT NOT NULL, tournament_team_id INT NOT NULL, PRIMARY KEY(tournament_id, tournament_team_id))');
        $this->addSql('CREATE INDEX IDX_1B60924333D1A3E7 ON tournament_tournament_team (tournament_id)');
        $this->addSql('CREATE INDEX IDX_1B609243426323A4 ON tournament_tournament_team (tournament_team_id)');
        $this->addSql('CREATE TABLE tournament_match (id INT NOT NULL, tournaments_id INT DEFAULT NULL, match_parent_id INT DEFAULT NULL, team_one_id INT DEFAULT NULL, team_two_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, team_one_win BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BB0D551CD92C1B5D ON tournament_match (tournaments_id)');
        $this->addSql('CREATE INDEX IDX_BB0D551C6BCB86DB ON tournament_match (match_parent_id)');
        $this->addSql('CREATE INDEX IDX_BB0D551C8D8189CA ON tournament_match (team_one_id)');
        $this->addSql('CREATE INDEX IDX_BB0D551CE6DD6E05 ON tournament_match (team_two_id)');
        $this->addSql('CREATE TABLE tournament_team (id INT NOT NULL, team_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F36D1421296CD8AE ON tournament_team (team_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, team_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(40) NOT NULL, slug VARCHAR(128) NOT NULL, is_verified BOOLEAN NOT NULL, description VARCHAR(255) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, firstname VARCHAR(30) DEFAULT NULL, lastname VARCHAR(30) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, newsletter BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, path VARCHAR(255) DEFAULT NULL, reddit_username VARCHAR(50) DEFAULT NULL, twitch_username VARCHAR(50) DEFAULT NULL, twitter_username VARCHAR(50) DEFAULT NULL, discord_server_token VARCHAR(20) DEFAULT NULL, youtube_username VARCHAR(50) DEFAULT NULL, is_closed BOOLEAN NOT NULL, date_subscribe DATE DEFAULT NULL, age INT DEFAULT NULL, gender VARCHAR(30) DEFAULT NULL, nb_participation INT DEFAULT NULL, nb_win INT DEFAULT NULL, elo INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649989D9B62 ON "user" (slug)');
        $this->addSql('CREATE INDEX IDX_8D93D649296CD8AE ON "user" (team_id)');
        $this->addSql('CREATE TABLE user_tournament_team (user_id INT NOT NULL, tournament_team_id INT NOT NULL, PRIMARY KEY(user_id, tournament_team_id))');
        $this->addSql('CREATE INDEX IDX_E1B178AEA76ED395 ON user_tournament_team (user_id)');
        $this->addSql('CREATE INDEX IDX_E1B178AE426323A4 ON user_tournament_team (tournament_team_id)');
        $this->addSql('CREATE TABLE user_relation (id INT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, status VARCHAR(30) NOT NULL, type VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8204A349F624B39D ON user_relation (sender_id)');
        $this->addSql('CREATE INDEX IDX_8204A349E92F8F78 ON user_relation (recipient_id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CB264F069 FOREIGN KEY (team_announcement_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C79376C75 FOREIGN KEY (tournament_announcement_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_type ADD CONSTRAINT FK_67CB3B05E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule ADD CONSTRAINT FK_FA1FEB4033D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule_equipe ADD CONSTRAINT FK_EE99353E26596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule_equipe ADD CONSTRAINT FK_EE99353E426323A4 FOREIGN KEY (tournament_team_id) REFERENCES tournament_team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule_match ADD CONSTRAINT FK_101664F426596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule_match ADD CONSTRAINT FK_101664F454E08946 FOREIGN KEY (equipe_une_id) REFERENCES poule_equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poule_match ADD CONSTRAINT FK_101664F46415CE33 FOREIGN KEY (equipe_deux_id) REFERENCES poule_equipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D93DDC24FE FOREIGN KEY (tournament_child_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_tournament_team ADD CONSTRAINT FK_1B60924333D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_tournament_team ADD CONSTRAINT FK_1B609243426323A4 FOREIGN KEY (tournament_team_id) REFERENCES tournament_team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551CD92C1B5D FOREIGN KEY (tournaments_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551C6BCB86DB FOREIGN KEY (match_parent_id) REFERENCES tournament_match (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551C8D8189CA FOREIGN KEY (team_one_id) REFERENCES tournament_team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551CE6DD6E05 FOREIGN KEY (team_two_id) REFERENCES tournament_team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D1421296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_tournament_team ADD CONSTRAINT FK_E1B178AEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_tournament_team ADD CONSTRAINT FK_E1B178AE426323A4 FOREIGN KEY (tournament_team_id) REFERENCES tournament_team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A349F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A349E92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE game_type DROP CONSTRAINT FK_67CB3B05E48FD905');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61FE48FD905');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D9E48FD905');
        $this->addSql('ALTER TABLE poule_equipe DROP CONSTRAINT FK_EE99353E26596FD8');
        $this->addSql('ALTER TABLE poule_match DROP CONSTRAINT FK_101664F426596FD8');
        $this->addSql('ALTER TABLE poule_match DROP CONSTRAINT FK_101664F454E08946');
        $this->addSql('ALTER TABLE poule_match DROP CONSTRAINT FK_101664F46415CE33');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D9D614C7E7');
        $this->addSql('ALTER TABLE announcement DROP CONSTRAINT FK_4DB9D91CB264F069');
        $this->addSql('ALTER TABLE tournament_team DROP CONSTRAINT FK_F36D1421296CD8AE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649296CD8AE');
        $this->addSql('ALTER TABLE announcement DROP CONSTRAINT FK_4DB9D91C79376C75');
        $this->addSql('ALTER TABLE poule DROP CONSTRAINT FK_FA1FEB4033D1A3E7');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D93DDC24FE');
        $this->addSql('ALTER TABLE tournament_tournament_team DROP CONSTRAINT FK_1B60924333D1A3E7');
        $this->addSql('ALTER TABLE tournament_match DROP CONSTRAINT FK_BB0D551CD92C1B5D');
        $this->addSql('ALTER TABLE tournament_match DROP CONSTRAINT FK_BB0D551C6BCB86DB');
        $this->addSql('ALTER TABLE poule_equipe DROP CONSTRAINT FK_EE99353E426323A4');
        $this->addSql('ALTER TABLE tournament_tournament_team DROP CONSTRAINT FK_1B609243426323A4');
        $this->addSql('ALTER TABLE tournament_match DROP CONSTRAINT FK_BB0D551C8D8189CA');
        $this->addSql('ALTER TABLE tournament_match DROP CONSTRAINT FK_BB0D551CE6DD6E05');
        $this->addSql('ALTER TABLE user_tournament_team DROP CONSTRAINT FK_E1B178AE426323A4');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318CB03A8386');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318C896DBBDE');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61FB03A8386');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61F896DBBDE');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D9B03A8386');
        $this->addSql('ALTER TABLE tournament DROP CONSTRAINT FK_BD5FB8D9896DBBDE');
        $this->addSql('ALTER TABLE user_tournament_team DROP CONSTRAINT FK_E1B178AEA76ED395');
        $this->addSql('ALTER TABLE user_relation DROP CONSTRAINT FK_8204A349F624B39D');
        $this->addSql('ALTER TABLE user_relation DROP CONSTRAINT FK_8204A349E92F8F78');
        $this->addSql('DROP SEQUENCE announcement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE poule_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE poule_equipe_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE poule_match_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE price_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tournament_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tournament_match_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tournament_team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE user_relation_id_seq CASCADE');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_type');
        $this->addSql('DROP TABLE poule');
        $this->addSql('DROP TABLE poule_equipe');
        $this->addSql('DROP TABLE poule_match');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_tournament_team');
        $this->addSql('DROP TABLE tournament_match');
        $this->addSql('DROP TABLE tournament_team');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_tournament_team');
        $this->addSql('DROP TABLE user_relation');
    }
}
