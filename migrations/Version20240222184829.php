<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222184829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_items ADD facture_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484457F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE INDEX IDX_BEF484457F2DEE08 ON cart_items (facture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484457F2DEE08');
        $this->addSql('DROP INDEX IDX_BEF484457F2DEE08 ON cart_items');
        $this->addSql('ALTER TABLE cart_items DROP facture_id');
    }
}
