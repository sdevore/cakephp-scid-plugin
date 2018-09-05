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
        $table->addColumn('scid_state', 'string', [
            'default' => null,
            'limit' => 32,
            'null' => true,
        ]);
        $table->addColumn('scid_ref_id', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => true,
        ]);
        $table->update();
    }
}
