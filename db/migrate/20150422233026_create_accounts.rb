class CreateAccounts < ActiveRecord::Migration
  def change
    create_table :accounts do |t|
      t.string :name
      t.string :email
      t.string :access_token
      t.string :dropbox_uid
      t.timestamps null: false
    end
  end
end
