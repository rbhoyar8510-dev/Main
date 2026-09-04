GRAM PANCHAYAT TIRRI - FULL PHP + MYSQL WEBSITE
====================================================

WHAT'S NEW IN THIS VERSION
------------------------------
- Real logo integration: your two logos (Maharashtra state seal + Gram
  Panchayat Tirri emblem) now appear in the site header AND on every
  issued certificate - assets/mh-gp.png and assets/gp-logo.png.
- login.php is now a "portal chooser" - a green Citizen Portal card and
  an orange Admin Portal card, like your reference screenshot.
- apply.php is now a single dynamic form - pick the certificate from a
  dropdown and the required document upload fields appear automatically.
- certificate_view.php - a proper certificate matching the Tehsildar
  format you shared: dual logo top-left, a real scannable barcode
  top-right (unique per certificate), office title, application details,
  numbered document list, and a "Signature valid ✅ / Digitally Signed"
  block at the bottom. Citizens can view/print it from their dashboard;
  admin can print it directly from the applications table.
- admin/applications.php redesigned: search by Token/Application Number,
  inline document pills (click to open), one-click Approve/Reject buttons
  right in the table, a WhatsApp button (opens a pre-filled status
  message to the applicant's number), and a Print button for approved
  certificates.
- db_test.php - a one-page tool to diagnose "Database not connected"
  errors with the EXACT MySQL error message (see below).

FIXING "DATABASE IS NOT CONNECTED" ON YOUR LIVE SITE
---------------------------------------------------------
Visit:  https://yourdomain.com/db_test.php
It will show the precise MySQL error (wrong host / wrong username /
wrong password / wrong database name / missing PHP MySQL extension) and
suggest the fix. Update includes/db.php accordingly, then re-check.
Delete db_test.php once everything works (it's not linked anywhere, so
it's safe to leave too, but best removed for tidiness).

IMPORTANT: RE-UPLOAD EVERYTHING (OVERWRITE), ESPECIALLY assets/style.css
-----------------------------------------------------------------------------
If your registration/login form looked "broken" (labels and boxes all
running together in one line instead of neatly stacked), that almost
always means your hosting is still serving an OLDER assets/style.css
from a previous upload. Please upload this ENTIRE zip's contents again
and overwrite every existing file - do not just upload the changed
pages. Browsers can also cache the old CSS - do a hard refresh
(Ctrl+Shift+R / long-press reload > "Empty cache and hard reload") after
uploading.

ADMIN LOGIN - HOW TO GET YOUR ID/PASSWORD
----------------------------------------------
For security, no admin username/password is hard-coded anywhere in this
project - passwords are always created fresh through your browser so
they're properly encrypted (bcrypt) and never sit in a plain-text file.
To create YOUR admin account:
  1. Open: https://yourdomain.com/admin/setup.php
  2. Enter any username, your name, and a password you choose.
  3. That's now your permanent Admin Portal login (admin/login.php).
This page automatically locks itself after the first admin is created,
so it's safe to leave in the project - visiting it again just redirects
to the login page.

STEP-BY-STEP SETUP (full)
------------------------------
1. In phpMyAdmin, open YOUR existing database (the one your host
   already created for you, e.g. usesr_42752856_grampanchayat_tirri).
2. Click Import -> choose database/schema.sql -> Go.
   (This file only creates tables, it never tries to create its own
   database, so it works on shared hosting.)
3. Edit includes/db.php with your exact DB host/name/username/password
   from your hosting panel.
4. Visit db_test.php to confirm the connection works.
5. Visit admin/setup.php to create your admin account.
6. Make uploads/documents writable: chmod 755 uploads/documents
7. Done - visit your site's login.php to see the new Citizen/Admin
   portal chooser.

LOGOS
------
Both logos are already placed in assets/ (mh-gp.png, gp-logo.png) and
wired into:
  - includes/header.php   (site header, every page)
  - certificate_view.php  (top-left of every issued certificate)
To change them later, just replace those two PNG files with the same
filenames - no code changes needed.

CERTIFICATE BARCODE & VERIFICATION
---------------------------------------
Each approved application gets a unique certificate number (e.g.
GPT/2026/93F1C2). certificate_view.php renders this as a real Code128
barcode (via a free public barcode-image service, so your server needs
normal internet access, which any live web host has). Anyone can type
that number into verify.php to confirm the certificate is genuine -
this also works as the "scan and check" flow: a staff member can scan
the printed barcode with any barcode-scanner app, which reads out the
certificate number, then type/paste it into verify.php.

WHAT'S NOT INCLUDED YET
----------------------------
Your reference screenshots also showed a much larger system (tax
bills, water connection tracking, gallery, complaints/तक्रार, नमुना 8,
scheme applications, photo-ID document previews inline, etc.). Those
are separate large modules beyond certificates - happy to build any of
them next, one at a time, once the certificate/login/admin flow above
is confirmed working on your live site.

RUNNING LOCALLY
------------------
    php -S localhost:8000
Then visit http://localhost:8000
