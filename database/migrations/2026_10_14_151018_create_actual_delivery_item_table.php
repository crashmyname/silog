<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateActualDeliveryItemTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('actual_delivery_item');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('delivery_id')->notNullable()->unsigned();
        $table->bigInteger('item_id')->notNullable()->unsigned();
        $table->integer('total_delivery')->default(0);
        $table->integer('plan_delivery')->default(0);
        $table->integer('actual_delivery')->default(0);
        $table->string('notes')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'actual_delivery_item' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('actual_delivery_item');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'actual_delivery_item' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
