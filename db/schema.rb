# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 20150423141532) do

  create_table "accounts", force: :cascade do |t|
    t.string   "name",         limit: 255
    t.string   "email",        limit: 255
    t.string   "access_token", limit: 255
    t.string   "dropbox_uid",  limit: 255
    t.datetime "created_at",               null: false
    t.datetime "updated_at",               null: false
  end

  create_table "folders", force: :cascade do |t|
    t.integer  "account_id",     limit: 4
    t.string   "name",           limit: 255
    t.string   "urlname",        limit: 255
    t.string   "password",       limit: 255
    t.datetime "created_at",                 null: false
    t.datetime "updated_at",                 null: false
    t.string   "plain_password", limit: 255
  end

  add_index "folders", ["account_id"], name: "index_folders_on_account_id", using: :btree

  add_foreign_key "folders", "accounts"
end
