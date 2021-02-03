<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210203113909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profil_recrut (id INT AUTO_INCREMENT NOT NULL, poste_id INT DEFAULT NULL, conditions VARCHAR(255) NOT NULL, INDEX IDX_ACC3A043A0905086 (poste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recrut (id INT AUTO_INCREMENT NOT NULL, poste VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profil_recrut ADD CONSTRAINT FK_ACC3A043A0905086 FOREIGN KEY (poste_id) REFERENCES recrut (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profil_recrut DROP FOREIGN KEY FK_ACC3A043A0905086');
        $this->addSql('DROP TABLE profil_recrut');
        $this->addSql('DROP TABLE recrut');
    }
}
