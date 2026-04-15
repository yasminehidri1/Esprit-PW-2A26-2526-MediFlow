# MediFlow Like & Comment Troubleshooting Guide

## Test the Database Functions

Before testing the UI, verify the backend works:

**Step 1: Run PHP functionality test**
- Open: `http://localhost/mediflow/test_functionality.php` in your browser
- Copy the output and share it with me

---

## Test the UI Manually

### Testing Likes:

**Step 1: Open an article**
- Go to `http://localhost/mediflow/frontOffice.php?action=view&id=1`

**Step 2: Open browser console (F12 → Console tab)**
- Note any error messages

**Step 3: Click the heart button (likes)**
- Watch the console for any `console.error()` messages
- Check the Network tab (F12 → Network) to see if the `frontOffice.php?action=like&id=1` request is sent
- What's the response? (should be JSON with `success: true` or `success: false`)

**Step 4: Check the database directly**
- Visit: `http://localhost/mediflow/debug_db.php`
- Does it show the likes count increasing?

---

### Testing Comments:

**Step 1: Scroll to comment section on the article page**

**Step 2: Type a test comment and submit**

**Step 3: Check browser console again**
- Any errors?
- Check Network tab for POST request to `frontOffice.php?action=add_comment`
- What's the response status?

**Step 4: Check the database**
- Visit `http://localhost/mediflow/test_functionality.php` again
- Does it show a new comment was created?

---

## Common Issues & Fixes

### Issue: "Like failed" toast appears
**Possible causes:**
- `frontOffice.js` not loading (check Network tab)
- Syntax error in JavaScript (check Console)
- AJAX request not reaching PHP (check Network tab)

**Fix:**
```bash
# Make sure the file exists and is readable
ls -la assets/js/frontOffice.js
```

### Issue: Comment shows UI success but not in database
**Possible causes:**
- Form validation failing silently
- Database constraint error (user ID 4 doesn't exist - but we verified it does)
- Transaction rollback

**Fix:**
- Check if `debug_db.php` confirms user 4 exists
- Run `test_functionality.php` to test comment creation directly

### Issue: No JavaScript errors but nothing happens
**Possible causes:**
- JavaScript events not initialized (DOM elements don't exist when JS runs)
- Wrong CSS selectors for buttons/forms

**Fix:**
```javascript
// Add to browser console (F12) to test manually:
fetch('frontOffice.php?action=like&id=1').then(r => r.json()).then(d => console.log(d))
```

---

## Share These Details

Please run the tests above and send me:

1. Output from `test_functionality.php`
2. Output from `debug_db.php`
3. Browser console errors (F12 → Console)
4. Network tab response for:
   - The like request (GET `frontOffice.php?action=like&id=1`)
   - The comment request (POST `frontOffice.php?action=add_comment`)
5. Any toast notifications you see (success or error)

This will help me pinpoint the exact issue!
