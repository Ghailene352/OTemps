<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212194219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // IMPORTANT: supprimer d'abord la FK et la colonne, PUIS la table tradition
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY `FK_497DD634649E4584`');
        $this->addSql('DROP INDEX IDX_497DD634649E4584 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP tradition_id');
        $this->addSql('DROP TABLE tradition');
        $this->addSql('ALTER TABLE objet CHANGE epoque epoque VARCHAR(255) DEFAULT NULL, CHANGE origine origine VARCHAR(255) DEFAULT NULL, CHANGE materiaux materiaux VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tradition (id INT AUTO_INCREMENT NOT NULL, nom_tradition VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, region VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE categorie ADD tradition_id INT NOT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT `FK_497DD634649E4584` FOREIGN KEY (tradition_id) REFERENCES tradition (id)');
        $this->addSql('CREATE INDEX IDX_497DD634649E4584 ON categorie (tradition_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE objet CHANGE epoque epoque VARCHAR(255) DEFAULT \'NULL\', CHANGE origine origine VARCHAR(255) DEFAULT \'NULL\', CHANGE materiaux materiaux VARCHAR(255) DEFAULT \'NULL\'');
    }
}
