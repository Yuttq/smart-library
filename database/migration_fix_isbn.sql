-- Migration script to fix ISBN constraint issues
-- Run this if you're getting duplicate ISBN errors

USE smart_library;

-- First, let's see if there are any duplicate ISBNs
-- SELECT isbn, COUNT(*) as count FROM books WHERE isbn IS NOT NULL AND isbn != '' GROUP BY isbn HAVING COUNT(*) > 1;

-- If there are duplicates, you can either:
-- 1. Delete the duplicates (keeping the first one)
-- 2. Update duplicates to have unique values
-- 3. Set duplicates to NULL

-- Option 1: Delete duplicate books (keeping the first one)
-- DELETE b1 FROM books b1
-- INNER JOIN books b2 
-- WHERE b1.id > b2.id 
-- AND b1.isbn = b2.isbn 
-- AND b1.isbn IS NOT NULL 
-- AND b1.isbn != '';

-- Option 2: Update duplicates to have unique values
-- UPDATE books SET isbn = CONCAT(isbn, '-', id) 
-- WHERE id IN (
--     SELECT id FROM (
--         SELECT b1.id 
--         FROM books b1
--         INNER JOIN books b2 
--         WHERE b1.id > b2.id 
--         AND b1.isbn = b2.isbn 
--         AND b1.isbn IS NOT NULL 
--         AND b1.isbn != ''
--     ) AS temp
-- );

-- Option 3: Set duplicate ISBNs to NULL (safest option)
UPDATE books SET isbn = NULL 
WHERE id IN (
    SELECT id FROM (
        SELECT b1.id 
        FROM books b1
        INNER JOIN books b2 
        WHERE b1.id > b2.id 
        AND b1.isbn = b2.isbn 
        AND b1.isbn IS NOT NULL 
        AND b1.isbn != ''
    ) AS temp
);

-- Now modify the ISBN column to allow NULL values
ALTER TABLE books MODIFY COLUMN isbn VARCHAR(20) UNIQUE NULL;

-- Verify the changes
-- SELECT * FROM books WHERE isbn IS NOT NULL ORDER BY isbn;
