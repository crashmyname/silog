<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateVehiclesTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('vehicles');
        
        //  Define columns here
        $table->id();
        $table->string('name')->notNullable();
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'vehicles' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('vehicles');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'vehicles' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
