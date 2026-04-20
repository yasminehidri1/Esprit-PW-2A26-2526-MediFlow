#!/usr/bin/env python3
import shutil
import os

# Copy the correct moderation file
src = 'c:\\xampp\\htdocs\\mediflow\\views\\backOffice\\moderation_new.php'
dst = 'c:\\xampp\\htdocs\\mediflow\\views\\backOffice\\moderation.php'

try:
    shutil.copy(src, dst)
    print(f"✓ Successfully copied {src} to {dst}")
    print("✓ Approve and Reject buttons removed!")
    print("✓ Comments now show delete option only!")
except Exception as e:
    print(f"Error: {e}")
