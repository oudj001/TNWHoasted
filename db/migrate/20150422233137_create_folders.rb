class CreateFolders < ActiveRecord::Migration
  def change
    create_table :folders do |t|
      t.belongs_to :account, index: true, foreign_key: true
      t.string :name
      t.string :urlname
      t.string :password
      t.timestamps null: false
    end
  end
end
