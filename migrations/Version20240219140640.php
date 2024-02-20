<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219140640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deposer (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persona (id INT AUTO_INCREMENT NOT NULL, deposer_id INT DEFAULT NULL, nom_du_groupe VARCHAR(255) NOT NULL, origine VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, annee_debut VARCHAR(255) NOT NULL, annee_separation VARCHAR(255) DEFAULT NULL, fodateurs VARCHAR(255) DEFAULT NULL, members INT DEFAULT NULL, courant_musical VARCHAR(255) DEFAULT NULL, presentation TEXT NOT NULL, INDEX IDX_51E5B69B788E566C (deposer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE persona ADD CONSTRAINT FK_51E5B69B788E566C FOREIGN KEY (deposer_id) REFERENCES deposer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persona DROP FOREIGN KEY FK_51E5B69B788E566C');
        $this->addSql('DROP TABLE deposer');
        $this->addSql('DROP TABLE persona');
    }
}
