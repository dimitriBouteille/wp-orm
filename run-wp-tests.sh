#!/usr/bin/env bash

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Load .env file if exists
if [[ -f .env ]]; then
    echo -e "${GREEN}Loading configuration from .env file...${NC}"
    export $(grep -v '^#' .env | xargs)
fi

# Default values
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-root}
MYSQL_DATABASE=${MYSQL_DATABASE:-wordpress_test}

echo -e "${GREEN}=== WordPress Test Environment Setup ===${NC}"
echo -e "Database: ${YELLOW}${MYSQL_DATABASE}${NC}"
echo ""

echo -e "${GREEN}Starting MySQL container...${NC}"
docker compose up -d mysql

# Wait for MySQL to be ready
echo -e "${GREEN}Waiting for MySQL to be ready...${NC}"
until docker compose exec -T mysql mysqladmin ping -h localhost -uroot -p${MYSQL_ROOT_PASSWORD} --silent &> /dev/null; do
    echo -e "${YELLOW}Waiting for MySQL...${NC}"
    sleep 2
done
echo -e "${GREEN}MySQL is ready!${NC}"
echo ""

echo -e "${GREEN}Creating test database...${NC}"
docker compose exec -T mysql mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "DROP DATABASE IF EXISTS ${MYSQL_DATABASE}; CREATE DATABASE ${MYSQL_DATABASE};" 2>/dev/null
echo -e "${GREEN}Database ready!${NC}"
echo ""

if [[ ! -d "vendor" ]]; then
    echo -e "${RED}Error: vendor directory not found. Please run 'composer install' first.${NC}" >&2
    exit 1
fi

# Install PHPUnit 9 (required for WordPress tests)
echo -e "${GREEN}Installing PHPUnit 9 (required for WordPress tests)...${NC}"
composer require --dev --update-with-all-dependencies 'phpunit/phpunit:^9.0' 'yoast/phpunit-polyfills:^3.0' --quiet

echo ""
echo -e "${GREEN}=== Running WordPress Tests ===${NC}"
echo ""

if [[ "$1" == "--coverage" ]]; then
    echo -e "${GREEN}Running tests with coverage...${NC}"
    composer run test:wordPress:coverage
else
    echo -e "${GREEN}Running tests without coverage...${NC}"
    composer run test:wordPress
fi

echo ""
echo -e "${GREEN}=== Tests completed! ===${NC}"
echo ""
echo -e "To stop MySQL container: ${YELLOW}docker compose down${NC}"
echo -e "To clean up everything: ${YELLOW}docker compose down -v${NC}"
