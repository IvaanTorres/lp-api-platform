<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315135034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ability_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ability (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, effect_change_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE pokemon_ability (pokemon_id INT NOT NULL, ability_id INT NOT NULL, PRIMARY KEY(pokemon_id, ability_id))');
        $this->addSql('CREATE INDEX IDX_59A592AD2FE71C3E ON pokemon_ability (pokemon_id)');
        $this->addSql('CREATE INDEX IDX_59A592AD8016D8B2 ON pokemon_ability (ability_id)');
        $this->addSql('ALTER TABLE pokemon_ability ADD CONSTRAINT FK_59A592AD2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_ability ADD CONSTRAINT FK_59A592AD8016D8B2 FOREIGN KEY (ability_id) REFERENCES ability (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon ADD pokemon_order INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD height INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD weight INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD image_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ability_id_seq CASCADE');
        $this->addSql('ALTER TABLE pokemon_ability DROP CONSTRAINT FK_59A592AD2FE71C3E');
        $this->addSql('ALTER TABLE pokemon_ability DROP CONSTRAINT FK_59A592AD8016D8B2');
        $this->addSql('DROP TABLE ability');
        $this->addSql('DROP TABLE pokemon_ability');
        $this->addSql('ALTER TABLE pokemon DROP pokemon_order');
        $this->addSql('ALTER TABLE pokemon DROP height');
        $this->addSql('ALTER TABLE pokemon DROP weight');
        $this->addSql('ALTER TABLE pokemon DROP image_path');
    }
}
