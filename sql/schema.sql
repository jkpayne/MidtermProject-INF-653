-- Quotes REST API — PostgreSQL schema
-- Create database first (as a superuser), then run this file against quotesdb:
--   createdb quotesdb
--   psql -d quotesdb -f sql/schema.sql

CREATE TABLE IF NOT EXISTS authors (
  id SERIAL PRIMARY KEY,
  author VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
  id SERIAL PRIMARY KEY,
  category VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS quotes (
  id SERIAL PRIMARY KEY,
  quote TEXT NOT NULL,
  author_id INTEGER NOT NULL,
  category_id INTEGER NOT NULL,
  CONSTRAINT fk_quotes_author
    FOREIGN KEY (author_id) REFERENCES authors (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_quotes_category
    FOREIGN KEY (category_id) REFERENCES categories (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);
