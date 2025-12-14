<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251213230317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assignment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, vehicle_id INTEGER NOT NULL, driver_id INTEGER NOT NULL, CONSTRAINT FK_30C544BA545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30C544BAC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_30C544BA545317D1 ON assignment (vehicle_id)');
        $this->addSql('CREATE INDEX IDX_30C544BAC3423909 ON assignment (driver_id)');
        $this->addSql('CREATE TABLE driver (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, license_category VARCHAR(16) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE maintenance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, work_type VARCHAR(255) NOT NULL, cost DOUBLE PRECISION NOT NULL, vehicle_id INTEGER NOT NULL, CONSTRAINT FK_2F84F8E9545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2F84F8E9545317D1 ON maintenance (vehicle_id)');
        $this->addSql('CREATE TABLE refuel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, liters DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, vehicle_id INTEGER NOT NULL, CONSTRAINT FK_B60345A1545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B60345A1545317D1 ON refuel (vehicle_id)');
        $this->addSql('CREATE TABLE route (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, start_location VARCHAR(255) NOT NULL, end_location VARCHAR(255) NOT NULL, distance DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE trip (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATETIME NOT NULL, distance DOUBLE PRECISION NOT NULL, fuel_consumed DOUBLE PRECISION DEFAULT NULL, vehicle_id INTEGER NOT NULL, driver_id INTEGER NOT NULL, CONSTRAINT FK_7656F53B545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7656F53BC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7656F53B545317D1 ON trip (vehicle_id)');
        $this->addSql('CREATE INDEX IDX_7656F53BC3423909 ON trip (driver_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TABLE vehicle (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vin VARCHAR(64) NOT NULL, plate_number VARCHAR(32) DEFAULT NULL, model VARCHAR(128) DEFAULT NULL, status VARCHAR(32) NOT NULL, mileage INTEGER NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1B80E486B1085141 ON vehicle (vin)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE assignment');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE refuel');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
