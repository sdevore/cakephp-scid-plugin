<?php
use Migrations\AbstractMigration;

class AddStateAndRefIdToPayments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('payments');
        $column = $table->hasColumn('scid_state');
        if (!$column) {
            $table->addColumn('scid_state', 'string', [
                'default' => null,
                'limit' => 32,
                'null' => true,
            ]);
        }
        $column = $table->hasColumn('scid_ref_id');
        if (!$column) {
            $table->addColumn('scid_ref_id', 'string', [
                'default' => NULL,
                'limit'   => 20,
                'null'    => TRUE,
            ]);
        }
        $table->update();
    }
}
