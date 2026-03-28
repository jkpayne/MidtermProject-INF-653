-- Seed data (idempotent upserts). Run: psql -d quotesdb -f sql/seed.sql

INSERT INTO authors (id, author) VALUES
  (1, 'Mark Twain'),
  (2, 'Albert Einstein'),
  (3, 'Maya Angelou'),
  (4, 'Steve Jobs'),
  (5, 'Oscar Wilde'),
  (6, 'Helen Keller'),
  (7, 'Winston Churchill')
ON CONFLICT (id) DO UPDATE SET author = EXCLUDED.author;

INSERT INTO categories (id, category) VALUES
  (1, 'Life'),
  (2, 'Success'),
  (3, 'Wisdom'),
  (4, 'Love'),
  (5, 'Technology'),
  (6, 'Motivation'),
  (7, 'Education')
ON CONFLICT (id) DO UPDATE SET category = EXCLUDED.category;

INSERT INTO quotes (id, quote, author_id, category_id) VALUES
  (1, 'The two most important days in your life are the day you are born and the day you find out why.', 1, 1),
  (2, 'Good friends, good books, and a sleepy conscience: this is the ideal life.', 1, 1),
  (3, 'Twenty years from now you will be more disappointed by the things you didn’t do than by the ones you did do.', 1, 2),
  (4, 'The secret of getting ahead is getting started.', 1, 2),
  (5, 'It is better to deserve honors and not have them than to have them and not deserve them.', 1, 3),
  (6, 'Life is short, break the rules, forgive quickly, kiss slowly, love truly, laugh uncontrollably.', 1, 4),
  (7, 'Imagination is more important than knowledge.', 2, 3),
  (8, 'Strive not to be a success, but rather to be of value.', 2, 2),
  (9, 'In the middle of difficulty lies opportunity.', 2, 6),
  (10, 'The only source of knowledge is experience.', 2, 7),
  (11, 'Life is like riding a bicycle. To keep your balance you must keep moving.', 2, 1),
  (12, 'I have learned that people will forget what you said, people will forget what you did, but people will never forget how you made them feel.', 3, 4),
  (13, 'You may encounter many defeats, but you must not be defeated.', 3, 6),
  (14, 'Nothing will work unless you do.', 3, 2),
  (15, 'We delight in the beauty of the butterfly, but rarely admit the changes it has gone through to achieve that beauty.', 3, 1),
  (16, 'Your work is going to fill a large part of your life, and the only way to be truly satisfied is to do what you believe is great work.', 4, 2),
  (17, 'Innovation distinguishes between a leader and a follower.', 4, 5),
  (18, 'Design is not just what it looks like and feels like. Design is how it works.', 4, 5),
  (19, 'Stay hungry, stay foolish.', 4, 6),
  (20, 'The people who are crazy enough to think they can change the world are the ones who do.', 4, 5),
  (21, 'Be yourself; everyone else is already taken.', 5, 1),
  (22, 'We are all in the gutter, but some of us are looking at the stars.', 5, 3),
  (23, 'Experience is simply the name we give our mistakes.', 5, 3),
  (29, 'Keep love in your heart. A life without it is like a sunless garden when the flowers are dead.', 5, 4),
  (30, 'Never love anyone who treats you like you are ordinary.', 5, 4),
  (24, 'The best way to predict the future is to create it.', 6, 2),
  (25, 'Alone we can do so little; together we can do so much.', 6, 4),
  (26, 'Success is not final, failure is not fatal: it is the courage to continue that counts.', 7, 2),
  (27, 'Continuous effort—not strength or intelligence—is the key to unlocking our potential.', 7, 6),
  (28, 'The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty.', 7, 3)
ON CONFLICT (id) DO UPDATE SET
  quote = EXCLUDED.quote,
  author_id = EXCLUDED.author_id,
  category_id = EXCLUDED.category_id;

SELECT setval(pg_get_serial_sequence('authors', 'id'), COALESCE((SELECT MAX(id) FROM authors), 1));
SELECT setval(pg_get_serial_sequence('categories', 'id'), COALESCE((SELECT MAX(id) FROM categories), 1));
SELECT setval(pg_get_serial_sequence('quotes', 'id'), COALESCE((SELECT MAX(id) FROM quotes), 1));
