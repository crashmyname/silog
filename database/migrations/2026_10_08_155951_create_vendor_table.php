<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateVendorTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('vendors');
        
        //  Define columns here
        $table->id();
        $table->string('code')->notNullable()->unique();
        $table->string('name')->notNullable();
        $table->string('contact');
        $table->string('phone');
        $table->string('email');
        $table->text('address');
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['name']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'vendor' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('vendors');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'vendor' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
