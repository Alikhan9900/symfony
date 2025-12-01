<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201224538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE branch RENAME INDEX address_id TO IDX_BB861B1FF5B7AF75');
        $this->addSql('DROP INDEX email ON client');
        $this->addSql('ALTER TABLE client RENAME INDEX address_id TO IDX_C7440455F5B7AF75');
        $this->addSql('ALTER TABLE employee CHANGE first_name first_name VARCHAR(100) NOT NULL, CHANGE last_name last_name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE employee RENAME INDEX branch_id TO IDX_5D9F75A1DCD6CC49');
        $this->addSql('ALTER TABLE maintenance_record CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE maintenance_record RENAME INDEX vehicle_id TO IDX_B1C9A998545317D1');
        $this->addSql('ALTER TABLE payment RENAME INDEX rental_id TO IDX_6D28840DA7CF2329');
        $this->addSql('ALTER TABLE rental CHANGE status status VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE rental RENAME INDEX client_id TO IDX_1619C27D19EB6921');
        $this->addSql('ALTER TABLE rental RENAME INDEX vehicle_id TO IDX_1619C27D545317D1');
        $this->addSql('ALTER TABLE rental RENAME INDEX employee_id TO IDX_1619C27D8C03F15C');
        $this->addSql('ALTER TABLE vehicle CHANGE vin vin VARCHAR(50) NOT NULL, CHANGE mileage mileage INT DEFAULT 0 NOT NULL, CHANGE status status VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX vin TO UNIQ_1B80E486B1085141');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX model_id TO IDX_1B80E4867975B7E7');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX branch_id TO IDX_1B80E486DCD6CC49');
        $this->addSql('ALTER TABLE vehicle_model CHANGE seats seats SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle_model RENAME INDEX manufacturer_id TO IDX_B53AF235A23B42D');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE branch RENAME INDEX idx_bb861b1ff5b7af75 TO address_id');
        $this->addSql('CREATE UNIQUE INDEX email ON client (email)');
        $this->addSql('ALTER TABLE client RENAME INDEX idx_c7440455f5b7af75 TO address_id');
        $this->addSql('ALTER TABLE employee CHANGE first_name first_name VARCHAR(100) DEFAULT NULL, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE employee RENAME INDEX idx_5d9f75a1dcd6cc49 TO branch_id');
        $this->addSql('ALTER TABLE maintenance_record CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE maintenance_record RENAME INDEX idx_b1c9a998545317d1 TO vehicle_id');
        $this->addSql('ALTER TABLE payment RENAME INDEX idx_6d28840da7cf2329 TO rental_id');
        $this->addSql('ALTER TABLE rental CHANGE status status VARCHAR(50) DEFAULT \'new\'');
        $this->addSql('ALTER TABLE rental RENAME INDEX idx_1619c27d19eb6921 TO client_id');
        $this->addSql('ALTER TABLE rental RENAME INDEX idx_1619c27d8c03f15c TO employee_id');
        $this->addSql('ALTER TABLE rental RENAME INDEX idx_1619c27d545317d1 TO vehicle_id');
        $this->addSql('ALTER TABLE vehicle CHANGE vin vin VARCHAR(50) DEFAULT NULL, CHANGE mileage mileage INT DEFAULT NULL, CHANGE status status VARCHAR(50) DEFAULT \'available\'');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX idx_1b80e486dcd6cc49 TO branch_id');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX idx_1b80e4867975b7e7 TO model_id');
        $this->addSql('ALTER TABLE vehicle RENAME INDEX uniq_1b80e486b1085141 TO vin');
        $this->addSql('ALTER TABLE vehicle_model CHANGE seats seats TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle_model RENAME INDEX idx_b53af235a23b42d TO manufacturer_id');
    }
}
