<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221212736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_BEF484451AD5CDBF (cart_id), UNIQUE INDEX UNIQ_BEF48445F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_cart DROP FOREIGN KEY FK_223BF558F347EFB');
        $this->addSql('ALTER TABLE produit_cart DROP FOREIGN KEY FK_223BF5581AD5CDBF');
        $this->addSql('DROP TABLE produit_cart');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7F347EFB');
        $this->addSql('DROP INDEX IDX_BA388B7F347EFB ON cart');
        $this->addSql('ALTER TABLE cart DROP produit_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_cart (produit_id INT NOT NULL, cart_id INT NOT NULL, INDEX IDX_223BF5581AD5CDBF (cart_id), INDEX IDX_223BF558F347EFB (produit_id), PRIMARY KEY(produit_id, cart_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE produit_cart ADD CONSTRAINT FK_223BF558F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_cart ADD CONSTRAINT FK_223BF5581AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484451AD5CDBF');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445F347EFB');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('ALTER TABLE cart ADD produit_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_BA388B7F347EFB ON cart (produit_id)');
    }
}
