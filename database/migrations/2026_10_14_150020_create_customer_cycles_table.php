<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateCustomerCyclesTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('customer_cycles');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('customer_id')->unsigned();
        $table->tinyInteger('cycle_number')->notNullable();
        $table->time('etd_from_ise')->nullable();
        $table->time('eta_at_customer')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['cycle_number', 'customer_id']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'customer_cycles' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('customer_cycles');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'customer_cycles' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
