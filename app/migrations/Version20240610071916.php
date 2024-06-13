<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610071916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE songs ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE songs ADD CONSTRAINT FK_BAECB19B12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_BAECB19B12469DE2 ON songs (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE songs DROP FOREIGN KEY FK_BAECB19B12469DE2');
        $this->addSql('DROP INDEX IDX_BAECB19B12469DE2 ON songs');
        $this->addSql('ALTER TABLE songs DROP category_id');
    }
}
