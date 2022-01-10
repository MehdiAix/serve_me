<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220107113145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_adress (id INT AUTO_INCREMENT NOT NULL, additional_address VARCHAR(255) DEFAULT NULL, street_number VARCHAR(10) NOT NULL, street_name VARCHAR(255) NOT NULL, postal_code INT NOT NULL, municipality VARCHAR(255) NOT NULL, departement VARCHAR(180) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD address_id INT DEFAULT NULL, ADD name VARCHAR(180) DEFAULT NULL, ADD firstname VARCHAR(180) DEFAULT NULL, ADD token_reset_password VARCHAR(255) DEFAULT NULL, ADD token_reset_password_expire VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES user_adress (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F5B7AF75 ON user (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('DROP TABLE user_adress');
        $this->addSql('DROP INDEX IDX_8D93D649F5B7AF75 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP address_id, DROP name, DROP firstname, DROP token_reset_password, DROP token_reset_password_expire, DROP created_at, DROP updated_at, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
