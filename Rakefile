require 'standalone_migrations'

StandaloneMigrations::Configurator.environments_config do |env|

  env.on "production" do

    if (ENV['DATABASE_URL'])
      db = URI.parse(ENV['DATABASE_URL'])
      return {
        adapter:  db.scheme == 'pgsql' ? 'postgresql' : db.scheme,
        host:     db.host,
        username: db.user,
        password: db.password,
        database: db.path[1..-1],
        encoding: 'utf8'
      }
    end

    nil
  end

end

StandaloneMigrations::Tasks.load_tasks

namespace :db do
  desc "Erase all tables"
  task :clear => :environment do
    conn = ActiveRecord::Base.connection
    tables = conn.tables
    tables.each do |table|
      puts "Deleting #{table}"
      conn.drop_table(table)
    end
  end
end