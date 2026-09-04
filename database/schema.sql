-- ============================================================
-- GRAM PANCHAYAT TIRRI - DATABASE SCHEMA
-- Import this file in phpMyAdmin / MySQL before using the DB-driven pages.
-- Works with MySQL 5.7+ / MariaDB 10+
--
-- IMPORTANT (shared hosting like InfinityFree/ByetCluster/Hostinger):
-- Your host already created a database for you (something like
-- usesr_42752856_grampanchayat_tirri). You do NOT have permission to create
-- a new database yourself, so this file does NOT run CREATE DATABASE / USE.
-- Just open YOUR existing database in phpMyAdmin first, then Import this
-- file into it - the tables will be created inside it directly.
-- ============================================================

-- ------------------------------------------------------------
-- 1. SETTINGS  (phone, email, address, site name, etc.)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(50) PRIMARY KEY,
  value_mr    VARCHAR(255) NOT NULL,
  value_en    VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, value_mr, value_en) VALUES
('site_name',   'ग्रामपंचायत तिर्री', 'Gram Panchayat Tirri'),
('phone',       '+91 XXXXX XXXXX', '+91 XXXXX XXXXX'),
('email',       'grampanchayat.tirri@example.com', 'grampanchayat.tirri@example.com'),
('address',     'तिर्री, पोस्ट - मिन्शी, तालुका - पवनी, जिल्हा - भंडारा, महाराष्ट्र', 'Tirri, Post Minshi, Taluka Pawani, District Bhandara, Maharashtra'),
('office_hours','सोमवार ते शनिवार, स. १०:०० ते सा. ५:००', 'Monday to Saturday, 10:00 AM - 5:00 PM'),
('population',  '—', '—'),
('households',  '—', '—'),
('wards',       '—', '—'),
('emergency_police',      '112 / 100', '112 / 100'),
('emergency_ambulance',   '108 / 102', '108 / 102'),
('emergency_electricity', '1912', '1912'),
('emergency_gp',          '07185-XXXXXX', '07185-XXXXXX'),
('water_schedule', 'सोमवार, बुधवार, शुक्रवार - सकाळी ६ ते ८ वाजेपर्यंत नळपुरवठा राहील.', 'Water supply on Monday, Wednesday, Friday - 6 AM to 8 AM.')
ON DUPLICATE KEY UPDATE value_mr = VALUES(value_mr), value_en = VALUES(value_en);


-- ------------------------------------------------------------
-- 2. MEMBERS  (Sarpanch, Up-Sarpanch, Members, Gram Sevak)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS members (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  position_mr   VARCHAR(100) NOT NULL,
  position_en   VARCHAR(100) NOT NULL,
  name          VARCHAR(150) NOT NULL DEFAULT '—',
  ward          VARCHAR(50)  NOT NULL DEFAULT '—',
  sort_order    INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO members (position_mr, position_en, name, ward, sort_order) VALUES
('सरपंच',     'Sarpanch',     '—', '—',      1),
('उपसरपंच',   'Up-Sarpanch',  '—', '—',      2),
('सदस्य',     'Member',       '—', '१ / 1',  3),
('सदस्य',     'Member',       '—', '२ / 2',  4),
('सदस्य',     'Member',       '—', '३ / 3',  5),
('ग्रामसेवक', 'Gram Sevak',   '—', '—',      6);


-- ------------------------------------------------------------
-- 3. SCHEMES / YOJANA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS schemes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title_mr    VARCHAR(255) NOT NULL,
  title_en    VARCHAR(255) NOT NULL,
  desc_mr     TEXT NOT NULL,
  desc_en     TEXT NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO schemes (title_mr, title_en, desc_mr, desc_en, sort_order) VALUES
('प्रधानमंत्री आवास योजना (ग्रामीण)', 'PM Awas Yojana (Gramin)',
 'बेघर व कच्च्या घरातील कुटुंबांना पक्के घर बांधण्यासाठी अनुदान.',
 'Housing assistance for homeless families and those living in kutcha houses.', 1),
('जल जीवन मिशन', 'Jal Jeevan Mission',
 'प्रत्येक घरापर्यंत नळाद्वारे शुद्ध पिण्याचे पाणी पोहोचवणे.',
 'Providing piped drinking water connection to every household.', 2),
('स्वच्छ भारत मिशन (ग्रामीण)', 'Swachh Bharat Mission (Gramin)',
 'वैयक्तिक व सार्वजनिक शौचालय बांधकाम व स्वच्छता जनजागृती.',
 'Construction of individual/public toilets and sanitation awareness.', 3),
('महात्मा गांधी राष्ट्रीय ग्रामीण रोजगार हमी योजना (मनरेगा)', 'MGNREGA',
 'ग्रामीण कुटुंबांना १०० दिवसांच्या रोजगाराची हमी.',
 'Guarantees 100 days of wage employment to rural households.', 4),
('प्रधानमंत्री मातृ वंदना योजना', 'PM Matru Vandana Yojana',
 'गरोदर व स्तनदा मातांना आर्थिक सहाय्य.',
 'Financial assistance for pregnant and lactating mothers.', 5),
('संजय गांधी निराधार अनुदान योजना', 'Sanjay Gandhi Niradhar Yojana',
 'निराधार, वृद्ध व विधवा व्यक्तींना मासिक अनुदान.',
 'Monthly pension for destitute, elderly and widowed persons.', 6);


-- ------------------------------------------------------------
-- 4. NOTICES / CIRCULARS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notices (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  notice_date   VARCHAR(30) NOT NULL,
  title_mr      VARCHAR(255) NOT NULL,
  title_en      VARCHAR(255) NOT NULL,
  desc_mr       TEXT NOT NULL,
  desc_en       TEXT NOT NULL,
  sort_order    INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO notices (notice_date, title_mr, title_en, desc_mr, desc_en, sort_order) VALUES
('2026', 'ग्रामसभेची सूचना', 'Gram Sabha Notice',
 'आगामी ग्रामसभेची तारीख व वेळ लवकरच जाहीर केली जाईल.',
 'Date and time of the upcoming Gram Sabha will be announced soon.', 1),
('2026', 'घरपट्टी भरणा सूचना', 'House Tax Payment Notice',
 'चालू आर्थिक वर्षाची घरपट्टी वेळेत भरण्याचे आवाहन.',
 'Residents are requested to pay house tax for the current financial year on time.', 2);


-- ------------------------------------------------------------
-- 5. CERTIFICATES (दाखले)  + their required documents
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS certificates (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title_mr    VARCHAR(255) NOT NULL,
  title_en    VARCHAR(255) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS certificate_docs (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  certificate_id  INT NOT NULL,
  doc_mr          VARCHAR(255) NOT NULL,
  doc_en          VARCHAR(255) NOT NULL,
  sort_order      INT NOT NULL DEFAULT 0,
  FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO certificates (id, title_mr, title_en, sort_order) VALUES
(1,  'रहिवासी (निवास) दाखला', 'Residence Certificate', 1),
(2,  'उत्पन्न दाखला', 'Income Certificate', 2),
(3,  'कुटुंब (फॅमिली) दाखला', 'Family Certificate', 3),
(4,  'वारस दाखला', 'Heir Certificate', 4),
(5,  'जन्म दाखला', 'Birth Certificate', 5),
(6,  'मृत्यू दाखला', 'Death Certificate', 6),
(7,  'विवाह दाखला (नोंद असल्यास)', 'Marriage Certificate (if registered)', 7),
(8,  'अविवाहित दाखला', 'Unmarried Certificate', 8),
(9,  'विधवा दाखला', 'Widow Certificate', 9),
(10, 'निराधार दाखला', 'Destitute (Nirashrit) Certificate', 10),
(11, 'घरपट्टी / मालमत्ता दाखला', 'House Tax / Property Certificate', 11),
(12, 'घर क्रमांक दाखला', 'House Number Certificate', 12),
(13, 'बांधकाम / एनओसी दाखला (लागू असल्यास)', 'Construction / NOC Certificate (if applicable)', 13),
(14, 'पाणी जोडणी दाखला', 'Water Connection Certificate', 14),
(15, 'रहिवास व चारित्र्य शिफारस', 'Residence & Character Recommendation', 15);

INSERT INTO certificate_docs (certificate_id, doc_mr, doc_en, sort_order) VALUES
(1, 'आधार कार्ड प्रत', 'Aadhar card copy', 1),
(1, 'रेशन कार्ड प्रत', 'Ration card copy', 2),
(1, 'विहित नमुन्यातील अर्ज', 'Application in prescribed format', 3),

(2, 'आधार कार्ड प्रत', 'Aadhar card copy', 1),
(2, 'उत्पन्नाचा स्वयंघोषणापत्र', 'Self-declaration of income', 2),
(2, 'रहिवासी पुरावा', 'Proof of residence', 3),

(3, 'रेशन कार्ड प्रत', 'Ration card copy', 1),
(3, 'सर्व सदस्यांचे आधार कार्ड', 'Aadhar of all family members', 2),

(4, 'मृत्यू दाखला प्रत', 'Death certificate copy', 1),
(4, 'वारसांचे आधार कार्ड', 'Aadhar cards of heirs', 2),
(4, 'प्रतिज्ञापत्र', 'Affidavit', 3),

(5, 'रुग्णालय जन्म नोंद', 'Hospital birth record', 1),
(5, 'पालकांचे आधार कार्ड', 'Parents Aadhar cards', 2),

(6, 'रुग्णालय / वैद्यकीय मृत्यू नोंद', 'Hospital/medical death record', 1),
(6, 'मृताचे आधार कार्ड', 'Deceased Aadhar card', 2),

(7, 'विवाह नोंदणी पुरावा', 'Marriage registration proof', 1),
(7, 'दोन्ही पक्षांचे आधार कार्ड', 'Aadhar cards of both parties', 2),

(8, 'आधार कार्ड प्रत', 'Aadhar card copy', 1),
(8, 'स्वयंघोषणापत्र / प्रतिज्ञापत्र', 'Self-declaration/affidavit', 2),

(9, 'पतीचा मृत्यू दाखला', 'Husband death certificate', 1),
(9, 'आधार कार्ड प्रत', 'Aadhar card copy', 2),

(10, 'उत्पन्नाचा दाखला', 'Income certificate', 1),
(10, 'स्वयंघोषणापत्र', 'Self-declaration', 2),

(11, 'घरपट्टी पावती', 'House tax receipt', 1),
(11, 'मालमत्ता नोंद उतारा', 'Property register extract', 2),

(12, 'घरपट्टी पावती', 'House tax receipt', 1),
(12, 'आधार कार्ड प्रत', 'Aadhar card copy', 2),

(13, 'जागेचा ७/१२ किंवा मालमत्ता उतारा', '7/12 extract or property record', 1),
(13, 'बांधकाम आराखडा', 'Construction plan', 2),

(14, 'घरपट्टी पावती', 'House tax receipt', 1),
(14, 'आधार कार्ड प्रत', 'Aadhar card copy', 2),

(15, 'आधार कार्ड प्रत', 'Aadhar card copy', 1),
(15, 'पोलीस पडताळणी (आवश्यक असल्यास)', 'Police verification (if required)', 2);


-- ------------------------------------------------------------
-- 6. USERS  (citizens who register to apply for certificates)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  full_name      VARCHAR(150) NOT NULL,
  mobile         VARCHAR(15)  NOT NULL UNIQUE,
  email          VARCHAR(150) NULL,
  password_hash  VARCHAR(255) NOT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 7. ADMINS  (Gram Panchayat staff who manage the site)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  username       VARCHAR(100) NOT NULL UNIQUE,
  full_name      VARCHAR(150) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- No admin is pre-created here. Open admin/setup.php the first time to create
-- the first admin account safely (it locks itself once one admin exists).


-- ------------------------------------------------------------
-- 8. APPLICATIONS  (certificate requests submitted by citizens)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS applications (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  application_number   VARCHAR(30) NOT NULL UNIQUE,
  user_id              INT NOT NULL,
  certificate_id       INT NOT NULL,
  applicant_name       VARCHAR(150) NOT NULL,
  applicant_mobile     VARCHAR(15)  NOT NULL,
  applicant_address    VARCHAR(255) NOT NULL,
  purpose              TEXT NULL,
  status               ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_remarks        TEXT NULL,
  certificate_number   VARCHAR(40) NULL UNIQUE,
  created_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (certificate_id) REFERENCES certificates(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 9. APPLICATION DOCUMENTS  (files uploaded by the citizen)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS application_documents (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  application_id  INT NOT NULL,
  doc_label       VARCHAR(150) NOT NULL,
  file_path       VARCHAR(255) NOT NULL,
  uploaded_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

