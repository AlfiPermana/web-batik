INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) 
VALUES (
    'Test User',
    'test@test.com',
    '$2y$12$IQDf7zF7Z7EZ7Z7Z7Z7Z7e7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7',
    'customer',
    NOW(),
    NOW(),
    NOW()
);
