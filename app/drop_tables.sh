#!/bin/bash

# Ścieżka do pliku .env
ENV_FILE=".env"

# Funkcja do pobierania wartości z pliku .env
get_env_value() {
  local key=$1
  grep -oP "(?<=${key}=).*" "$ENV_FILE" | tr -d '"'
}

# Pobieranie wartości DATABASE_URL z pliku .env
DATABASE_URL=$(get_env_value "DATABASE_URL")

# Wyświetlanie wartości DATABASE_URL dla debugowania
echo "DATABASE_URL: $DATABASE_URL"

# Rozdzielanie URL do odpowiednich części
DB_USER=$(echo $DATABASE_URL | grep -oP '(?<=//)[^:]*')
DB_PASSWORD=$(echo $DATABASE_URL | grep -oP '(?<=:)[^:]*?(?=@)')
DB_HOST=$(echo $DATABASE_URL | grep -oP '(?<=@)[^:]*')
DB_PORT=$(echo $DATABASE_URL | grep -oP '(?<=:)[0-9]+(?=/)')
DB_NAME=$(echo $DATABASE_URL | sed -n 's#.*/\([^?]*\).*#\1#p')

# Wyświetlanie wartości dla debugowania
echo "DB_USER: $DB_USER"
echo "DB_PASSWORD: $DB_PASSWORD"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_NAME: $DB_NAME"

# SQL do dropowania tabel
SQL_DROP_TABLES="DROP TABLE IF EXISTS tag, song, category, song_tag, doctrine_migration_versions;"

# Wykonanie polecenia MySQL
mysql -u"$DB_USER" -p"$DB_PASSWORD" -h"$DB_HOST" -P"$DB_PORT" "$DB_NAME" -e "$SQL_DROP_TABLES"

echo "Tabele zostały usunięte."
