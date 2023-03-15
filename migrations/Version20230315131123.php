<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315131123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE pokemon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pokemon (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_pokemon (type_id INT NOT NULL, pokemon_id INT NOT NULL, PRIMARY KEY(type_id, pokemon_id))');
        $this->addSql('CREATE INDEX IDX_4AFDFF06C54C8C93 ON type_pokemon (type_id)');
        $this->addSql('CREATE INDEX IDX_4AFDFF062FE71C3E ON type_pokemon (pokemon_id)');
        $this->addSql('ALTER TABLE type_pokemon ADD CONSTRAINT FK_4AFDFF06C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_pokemon ADD CONSTRAINT FK_4AFDFF062FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE pokemon_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_id_seq CASCADE');
        $this->addSql('ALTER TABLE type_pokemon DROP CONSTRAINT FK_4AFDFF06C54C8C93');
        $this->addSql('ALTER TABLE type_pokemon DROP CONSTRAINT FK_4AFDFF062FE71C3E');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE type_pokemon');
    }
}
