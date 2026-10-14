<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateActualDeliveriesTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('actual_deliveries');
        
        //  Define columns here
        $table->id();
        $table->date('delivery_date')->notNullable();
        $table->bigInteger('customer_id')->unsigned();
        $table->bigInteger('customer_cycle_id')->unsigned();
        $table->bigInteger('vehicle_id')->unsigned();
        $table->string('vehicle_note');
        $table->time('etd_plan');
        $table->time('eta_plan');
        $table->time('actual_departure');
        $table->time('actual_arrival');
        $table->enum('status',['waiting','on_the_way','arrived','delivered','delayed','cancelled'])->default('waiting');
        $table->text('notes')->nullable();
        $table->bigInteger('created_by')->nullable();
        $table->boolean('is_active')->default(1);
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['delivery_date', 'customer_id','status']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'actual_deliveries' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('actual_deliveries');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'actual_deliveries' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
