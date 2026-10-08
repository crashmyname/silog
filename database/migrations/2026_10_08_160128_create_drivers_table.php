<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateDriversTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('drivers');
        
        //  Define columns here
        $table->id();
        $table->string('name')->notNullable();
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name', 'slug']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'drivers' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('drivers');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'drivers' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}

// -- VENDORS
// CREATE TABLE vendors (
//   id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   code       VARCHAR(30)  NOT NULL UNIQUE,
//   name       VARCHAR(120) NOT NULL,
//   contact    VARCHAR(100),
//   phone      VARCHAR(30),
//   email      VARCHAR(100),
//   address    TEXT,
//   is_active  TINYINT(1)   DEFAULT 1,
//   created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
//   updated_at DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB;

// -- DRIVERS
// CREATE TABLE drivers (
//   id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   nip          VARCHAR(30)  NOT NULL UNIQUE,
//   name         VARCHAR(100) NOT NULL,
//   phone        VARCHAR(30),
//   license_type ENUM('B1','B2','B2 Umum','C') DEFAULT 'B2 Umum',
//   vendor_id    BIGINT UNSIGNED NULL,
//   status       ENUM('active','leave','inactive') DEFAULT 'active',
//   created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
//   updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//   FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
//   INDEX idx_status (status)
// ) ENGINE=InnoDB;

// -- VEHICLES
// CREATE TABLE vehicles (
//   id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   plate_number  VARCHAR(20) NOT NULL UNIQUE,
//   type          ENUM('Truk','Trailer','Wingbox','Pickup','Van') DEFAULT 'Truk',
//   brand         VARCHAR(60),
//   year          SMALLINT,
//   capacity_ton  DECIMAL(8,2),
//   vendor_id     BIGINT UNSIGNED NULL,
//   status        ENUM('available','in_use','maintenance','inactive') DEFAULT 'available',
//   created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
//   updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//   FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
//   INDEX idx_status (status),
//   INDEX idx_type   (type)
// ) ENGINE=InnoDB;

// -- WAREHOUSES
// CREATE TABLE warehouses (
//   id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   code         VARCHAR(30)  NOT NULL UNIQUE,
//   name         VARCHAR(120) NOT NULL,
//   location     VARCHAR(200),
//   capacity_m2  DECIMAL(10,2),
//   pic_name     VARCHAR(100),
//   status       ENUM('active','inactive') DEFAULT 'active',
//   created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
//   updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB;

// -- ROUTES
// CREATE TABLE routes (
//   id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   code         VARCHAR(30)  NOT NULL UNIQUE,
//   origin       VARCHAR(100) NOT NULL,
//   destination  VARCHAR(100) NOT NULL,
//   distance_km  DECIMAL(8,2),
//   duration_h   DECIMAL(5,2),
//   tariff       DECIMAL(15,2),
//   created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
//   updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//   INDEX idx_origin      (origin),
//   INDEX idx_destination (destination)
// ) ENGINE=InnoDB;

// -- CUSTOMERS
// CREATE TABLE customers (
//   id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   code       VARCHAR(30)  NOT NULL UNIQUE,
//   name       VARCHAR(120) NOT NULL,
//   contact    VARCHAR(100),
//   phone      VARCHAR(30),
//   address    TEXT,
//   status     ENUM('active','inactive') DEFAULT 'active',
//   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
//   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB;