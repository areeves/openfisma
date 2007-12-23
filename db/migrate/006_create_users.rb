class CreateUsers < ActiveRecord::Migration
  def self.up
    create_table :users do |t|
      t.column :first_name, :string
      t.column :last_name, :string
      t.column :email_address, :string
      t.column :password_hash, :string
      t.column :password_salt, :string
      t.column :created_at, :datetime
      t.column :updated_at, :datetime
      t.column :number_mobile, :integer
      t.column :number_work, :integer
    end
  end

  def self.down
    drop_table :users
  end
end
