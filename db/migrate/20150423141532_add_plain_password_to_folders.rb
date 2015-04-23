class AddPlainPasswordToFolders < ActiveRecord::Migration
  def change
    add_column :folders, :plain_password, :string
  end
end
