#!/bin/bash
# Fix npm security vulnerabilities

echo "Fixing npm security vulnerabilities..."
npm audit fix

# If the regular fix doesn't work, try force fixing (only use if needed)
if [ $? -ne 0 ]; then
    echo "Trying with force flag..."
    npm audit fix --force
fi

# Update packages that might resolve security issues
echo "Updating key packages..."
npm update

echo "Security fix completed. Check the output for any remaining issues."
