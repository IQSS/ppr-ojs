#!/bin/bash

# Read the current version from the version file
CURRENT_VERSION=$(cat VERSION)

# Extract the last number from the current version
LAST_NUMBER=$(echo $CURRENT_VERSION | awk -F. '{print $NF}')

# Increment the last number by one
NEW_LAST_NUMBER=$((LAST_NUMBER+1))

# Replace the last number in the current version with the new last number
NEW_VERSION=$(echo $CURRENT_VERSION | sed "s/\.$LAST_NUMBER$/.${NEW_LAST_NUMBER}/")

# Write the new version to the version file
echo $NEW_VERSION > VERSION
