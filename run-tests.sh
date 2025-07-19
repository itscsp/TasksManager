#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print section header
print_header() {
    echo -e "${YELLOW}\n=== $1 ===${NC}\n"
}

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Please install composer first."
    exit 1
fi

# Install dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    print_header "Installing dependencies"
    composer install
fi

# Parse command line arguments
COVERAGE=0
FILTER=""
FILE=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --coverage)
            COVERAGE=1
            shift
            ;;
        --filter=*)
            FILTER="${1#*=}"
            shift
            ;;
        --file=*)
            FILE="${1#*=}"
            shift
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Run tests based on arguments
if [ $COVERAGE -eq 1 ]; then
    print_header "Running tests with coverage"
    ./vendor/bin/phpunit --coverage-text
elif [ ! -z "$FILTER" ]; then
    print_header "Running tests with filter: $FILTER"
    ./vendor/bin/phpunit --filter "$FILTER"
elif [ ! -z "$FILE" ]; then
    print_header "Running specific test file: $FILE"
    ./vendor/bin/phpunit "tests/unit/$FILE"
else
    print_header "Running all tests"
    ./vendor/bin/phpunit
fi
