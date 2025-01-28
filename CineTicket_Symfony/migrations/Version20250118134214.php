<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118134214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, movie_id INT NOT NULL, utilisateur_id INT NOT NULL, screening_id INT NOT NULL, seats JSON NOT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, comment VARCHAR(255) NOT NULL, INDEX IDX_42C849558F93B6FC (movie_id), INDEX IDX_42C84955FB88E14F (utilisateur_id), INDEX IDX_42C8495570F5295D (screening_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495570F5295D FOREIGN KEY (screening_id) REFERENCES screening (id)');
        $this->addSql('ALTER TABLE utilisateur CHANGE profile_image profile_image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558F93B6FC');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FB88E14F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495570F5295D');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE utilisateur CHANGE profile_image profile_image VARCHAR(255) DEFAULT NULL');
    }
}
